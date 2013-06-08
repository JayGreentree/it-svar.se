<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Dropdown Link Style</th>
            <th style="width:250px">Type</th>
            <th>CSS Styles & Classes</th>
            <th>HTML Attributes</th>
            <!-- <th>Wrapper</th> -->
            <th style="width:20px;"></th>
        </tr>
    </thead>
    <tbody class="styles_rows">
        <?php foreach($styles as $style):?>
        <tr>
            <td><?=form_input('settings[styles][][title]', $style['title'])?></td>
            <td>
                <?=form_input('settings[styles][][btn_style]', $style['btn_style'], 'placeholder="CSS Styles"')?>
                <?=form_input('settings[styles][][btn_class]', $style['btn_class'], 'placeholder="CSS Class"')?><br>
            </td>
            <td class="typesel">
                <?=form_dropdown('settings[styles][][type]', array('inline' => 'Inline', 'block' => 'Block', 'current' => 'Current', 'custom' => 'Custom'), $style['type'], 'style="padding:3px; width:90px;"');?>
                <?=form_input('settings[styles][][custom_type]', $style['custom_type'])?>
            </td>
            <td>
                <?=form_input('settings[styles][][styles]', $style['styles'], 'placeholder="CSS Styles"')?>
                <?=form_input('settings[styles][][classes]', $style['classes'], 'placeholder="CSS Classes"')?>

                <input type="hidden" name="settings[styles][][wrapper]" value="">
                <input type="hidden" name="settings[styles][][type_value]" value="">
            </td>
            <td><?=form_input('settings[styles][][attr]', $style['attr'])?></td>
<!--             <td>
                <?=form_dropdown('settings[styles][][wrapper]', array('no' => 'No', 'yes' => 'Yes'), $style['wrapper']);?>
            </td>
-->
            <td>
                <a style="position: relative;top:0;" class="abtn delete" href="#"><span style="width:16px;padding:0">&nbsp;</span></a>
            </td>
        </tr>
        <?php endforeach;?>

        <tr class="dummy" style="display:none">
            <td><?=form_input('settings[styles][][title]', '')?></td>
            <td>
                <?=form_input('settings[styles][][btn_style]', '', 'placeholder="CSS Styles"')?><br>
                <?=form_input('settings[styles][][btn_class]', '', 'placeholder="CSS Class"')?>
            </td>
            <td class="typesel">
                <?=form_dropdown('settings[styles][][type]', array('inline' => 'Inline', 'block' => 'Block', 'current' => 'Current', 'custom' => 'Custom'), '', 'style="padding:3px; width:90px;"');?>
                <?=form_input('settings[styles][][custom_type]', 'div')?>
            </td>
            <td>
                <?=form_input('settings[styles][][styles]', '', 'placeholder="CSS Styles"')?><br>
                <?=form_input('settings[styles][][classes]', '', 'placeholder="CSS Classes"')?>

                <input type="hidden" name="settings[styles][][wrapper]" value="">
                <input type="hidden" name="settings[styles][][type_value]" value="">
            </td>
            <td><?=form_input('settings[styles][][attr]', '')?></td>
<!--             <td>
                <?=form_dropdown('settings[styles][][wrapper]', array('no' => 'No', 'yes' => 'Yes'));?>
            </td>
-->
            <td>
                <a style="position: relative;top:0;" class="abtn delete" href="#"><span style="width:16px;padding:0">&nbsp;</span></a>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9"><a href="#" class="submit add_style">Add New Style</a></td>
        </tr>
    </tfoot>
</table>

<h2>Help</h2>
<table>
    <tbody>
    <tr>
        <td><strong>Title</strong> [required]</td>
        <td>Label for this style item</td>
        <td><strong>Type</strong> [required]</td>
        <td>
            <ul>
                <!-- <li><strong>Selector:</strong> limits the style to a specific HTML tag, and will apply the style to an existing tag instead of creating one</li> -->
                <li><strong>Block:</strong> creates a new block-level element with the style applied, and will replace the existing block element around the cursor</li>
                <li><strong>Inline:</strong> creates a new inline element with the style applied, and will wrap whatever is selected in the editor, not replacing any tags</li>
                <li><strong>Current:</strong> applies the style to the current selected element</li>
            </ul>
        </td>
    </tr>
    <tr>
        <td><strong>CSS Classes</strong> [optional]</td>
        <td>Space-separated list of classes to apply to the element</td>
        <td><strong>CSS Styles</strong> [optional]</td>
        <td>List of css styles to apply to the element, example: font-weight:bold; font-size:30px;</td>
    </tr>
    <tr>
        <td><strong>Attributes</strong> [optional]</td>
        <td>Assigns attributes to the element (same syntax as <strong>CSS Classes</strong>)</td>
        <td></td>
        <td></td>
        <!--<td><strong>Wrapper</strong> [optional]</td>
        <td>If set to <strong>Yes</strong>, creates a new block-level element around any selected block-level elements</td>-->
    </tr>
    <tr>
        <td colspan="4">Note that while classes and styles are both optional, one of the two should be present. (Otherwise, why are you adding this to the dropdown anyway?)</td>
    </tr>
</tbody>
</table>
