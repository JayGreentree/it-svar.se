<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>HTML Template</th>
            <th style="width:20px;"></th>
        </tr>
    </thead>
    <tbody class="templates_rows">
        <?php foreach($templates as $template):?>
        <tr>
            <td><?=form_input('settings[templates][][title]', $template['title'])?></td>
            <td><?=form_textarea('settings[templates][][html]', $template['html'])?></td>
            <td>
                <a style="position: relative;top:0;" class="abtn delete" href="#"><span style="width:16px;padding:0">&nbsp;</span></a>
            </td>
        </tr>
        <?php endforeach;?>

        <tr class="dummy" style="display:none">
            <td><?=form_input('settings[templates][][title]', '')?></td>
            <td><?=form_textarea('settings[templates][][html]', '')?></td>
            <td>
                <a style="position: relative;top:0;" class="abtn delete" href="#"><span style="width:16px;padding:0">&nbsp;</span></a>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9"><a href="#" class="submit add_template">Add New Template</a></td>
        </tr>
    </tfoot>
</table>
