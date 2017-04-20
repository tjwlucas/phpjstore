    <div id='<?= $key ?>_settings'></div>
    <span class="pull-right">
    <button id='<?= $key ?>_submit' class="btn btn-primary">Save <?= $key ?></button>
    </span>

    <script>
    
    // Initialize the editor with a JSON schema
    var <?= $key ?>_editor = new JSONEditor(document.getElementById('<?= $key ?>_settings'),{
    theme: 'bootstrap3',
    iconlib: "bootstrap3",
    disable_edit_json: true,
    disable_properties: true,
    disable_collapse: true,
    //remove_empty_properties: true,
        schema: <?php include($this->datapath."/schemas/".$key.".json"); ?>
    });
    
    // Hook up the submit button to log to the console
    document.getElementById('<?= $key ?>_submit').addEventListener('click',function() {
        /*$.post( "/admin/settings/set/<?= $key ?>", { "settings": JSON.stringify(<?= $key ?>_editor.getValue(), null, 2) } , function( data ) {
            location.reload();
        });*/
        alert(JSON.stringify(<?= $key ?>_editor.getValue(), null, 2));
    });
    </script>