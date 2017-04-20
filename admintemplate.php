    <div class="row">
        <div id='<?= $key ?>_settings'></div>
            <span class="pull-right">
            <button id='<?= $key ?>_submit' class="btn btn-primary">Save <?= $key ?></button>
        </span>    
    </div>

    <script>
    
    // Initialize the editor with a JSON schema
    var <?= $key ?>_editor = new JSONEditor(document.getElementById('<?= $key ?>_settings'),{
    theme: 'bootstrap3',
    iconlib: "bootstrap3",
    disable_edit_json: true,
    disable_properties: true,
    disable_collapse: true,
    //remove_empty_properties: true,
        schema: <?= $schema ?>
    });
    
    // Hook up the submit button to log to the console
    document.getElementById('<?= $key ?>_submit').addEventListener('click',function() {
        $.post( "<?=$this->adminpost?>", { "key": "<?= $key ?>", "json": JSON.stringify(<?= $key ?>_editor.getValue(), null, 2) } , function( data ) {
            location.reload();
        });
    });
    </script>
