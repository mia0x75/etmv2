<?php //defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('mysql.connect_timeout', '3000');
//ini_set('default_socket_timeout', '3000');
ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Pheal\Core\Config;
use Pheal\Pheal;

class Autoexec_updater extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->cache_off();
        $this->db->cache_delete_all();
        $this->load->model('common/Log');
        $this->load->model('common/Msg');
        $this->load->model('internal/Autoexec_updater_model');
        $this->load->model('Updater_model');
        $this->load->model('common/ValidateRequest');
    }

    /**
     * Kickstarts the daily update procedure for every user
     * @return [type] [description]
     */
    public function index()
    {
    	if (!$this->input->is_cli_request()) {
            die();
        }
        
        try {
           $chars = $this->Autoexec_updater_model->getAllUsers(); 
       } catch (Throwable $e) {
            echo sprintf(
                "an exception was caught! Type: %s Message: %s",
                get_class($e),
                $e->getMessage()
            );
       }
        
        foreach ($chars as $row) {
            $start    = microtime(true);
            $username = $row->username;
            $iduser = (int) $row->iduser;
            echo $username . "->" . $iduser . "\n";

            try {
                $this->Updater_model->init($username);
            } catch (Throwable $e) {
                $this->Updater_model->release($username);
                log_message('error', $e->getMessage());
                echo sprintf(
                    "an exception was caught! Type: %s Message: %s",
                    get_class($e),
                    $e->getMessage()
                );
            }

            //check if API server is up
            if (!$this->ValidateRequest->testEndpoint()) {
                echo XML_CONNECT_FAILURE;
            } else {
                if ($this->Updater_model->isLocked($username)) {
                    echo "User already updating. Aborting..." . "\n";
                } else {
                    //check if user has any keys
                    $keys = $this->Updater_model->getKeys($username);
                    if (count($keys) == 0) {
                        echo "No keys" . "\n";
                    } else {
                        //validate existing keys
                        if (!$this->Updater_model->processAPIKeys($keys, $username)) {
                            echo "No valid keys" . "\n";
                        } else {
                            //begin update
                            $this->Updater_model->lock($username);
                            try {
                                $result_iterate = $this->Updater_model->iterateAccountCharacters();
                                if (!$result_iterate) {
                                    echo Msg::DB_ERROR . "\n";
                                } else {
                                	//if we arrived here, that means nothing went wrong (yet)
							        //calculate profits
                                    try {
                                        $this->db->trans_start();
                                        $this->Updater_model->calculateProfits();
                                        //totals and history
                                        $this->Updater_model->updateTotals();
                                        $this->db->trans_complete();
                                    } catch (Throwable $e) {
                                        $this->Updater_model->release($username);
                                        log_message('error', $e->getMessage());
                                        echo sprintf(
                                            "an exception was caught! Type: %s Message: %s",
                                            get_class($e),
                                            $e->getMessage()
                                        );
                                    }
							        
                                    $finish = microtime(true);
                                    echo number_format($finish - $start, 2) . " for username " . $username . "\n";
                                    $this->Updater_model->release($username);
                                }
                            } catch (Throwable $e) {
                                //if an exception happens during update (this is a bug on Eve's API)
                                echo sprintf(
                                    "an exception was caught! Type: %s Message: %s",
                                    get_class($e),
                                    $e->getMessage()
                                );
                                $this->Updater_model->release($username);

                                //cache is now corrupted for 24 hours, remove cache and try again
                                //remove all keys just in case
                                //$problematicKeys = $this->Updater_model->getAPIKeys($this->user_id);
                                foreach ($keys as $row) {
                                	echo 'deleting cache. canceled update ' . $iduser;
                                    $key = $row['apikey'];

                                    $dir = FILESTORAGE . $key;
                                    $this->removeDirectory($dir);
                                    $this->Log->addEntry('clear', $iduser);
                                    //release the lock
                                }
                            }
                        }
                    }
                }
            }
            //make sure lock is released in case of exceptions, timeouts, ...
            $this->Updater_model->release($username);
        }
    }

    /**
     * Removes a directory when cache files are corrupt
     * @param  [type] $path 
     * @return void       
     */
    private function removeDirectory($path) : void
    {
        if (is_dir($path)) {
            $files = glob($path . '/*');
            foreach ($files as $file) {
                is_dir($file) ? $this->removeDirectory($file) : unlink($file);
            }
            rmdir($path);
        }
    }
}
