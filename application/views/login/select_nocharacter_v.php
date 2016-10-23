<script src="<?=base_url('dist/js/apps/loginapikeymanagement-app.js')?>?HASH_CACHE=<?=HASH_CACHE?>"></script>
</script>
<section class="content" data-url="<?=base_url()?>">
    <div class="back-link">
        <a class="btn btn-accent" href="<?=base_url('index/main')?>">
            Back to main page
        </a>
    </div>
    <div class="container-center animated slideInDown">
        <div class="view-header">
            <div class="header-icon">
                <i class="pe page-header-icon pe-7s-key">
                </i>
            </div>
            <div class="header-title">
                <h3>
                    Add API Key
                </h3>
                <small>
                    It seems like you have no characters in your ETM account.
                    This is because you either removed every character from your account or your API keys have expired and were automatically purged.
                    <h5>Please add a new valid API Key to continue</h5>
                </small>
            </div>
        </div>
        <div class="panel panel-filled">
            <div class="panel-body">
                <form class="form-horizontal add-apikey" data-url="<?=base_url()?>" name="apiform">
                    <div class="api-insert-1">
                        <div>
                            <span class="help-block small text-center">
                                <a href="https://community.eveonline.com/support/api-key/CreatePredefined?accessMask=82317323" target="_blank">Generate key HERE</a>. ETM only accepts keys created with this link.
                            </span><br>
                        </div>
                        <div class="form-group"><label for="keyid" class="col-sm-2 control-label">KeyID</label>
                            <div class="col-sm-10"><input type="number" class="form-control list-name" id="keyid" name="keyid" placeholder="Type your Key ID here" autofocus></div>
                        </div>
                        <div class="form-group"><label for="keyid" class="col-sm-2 control-label">vCode</label>
                            <div class="col-sm-10"><input type="text" class="form-control list-name" id="vcode" name="vcode" placeholder="Type your vCode here" autofocus></div>
                        </div>
                        <button type="submit" class="btn btn-default submit-add" name="submit-add">Submit</button>
                    </div>
                    <div class="api-insert-2 text-center">
                        <span class="help-block small text-center">Select which characters you would like to import to your account: </span>
                        <div class="table-responsive">
                            <form class="form-horizontal add-character" autofill="false" name="apiform">
                                <table class="table table-responsive table-stripped table-hover table-character-selection table-nochars">
                                    <thead>
                                        <tr>
                                            <th>Character</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-chars">
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <button type="submit" class="btn btn-default submit-add-2" name="submit-add">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
