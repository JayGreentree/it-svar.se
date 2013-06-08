<?php echo $this->view('menu'); ?>
<div class="dtable" style="margin:0">
    <h2 style="padding:9px 9px 5px">
        <?=lang('ed:btn_settings')?>: <?=$btn->info['name']?>
    </h2>
</div>

<div id="dbody">

<?=form_open($base_url_short.AMP.'method=buttons_save')?>

    <div class="dtable">
        <h2><?=$btn->info['name']?></h2>
        <table>
            <tbody>
                <tr>
                    <td><label>Class</label></td>
                    <td><?=$btn_class?></td>
                    <td><label><?=lang('ed:version')?></label></td>
                    <td><?php if (isset($btn->info['version']) === TRUE) echo $btn->info['version']?></td>
                </tr>
                <tr>
                    <td><label><?=lang('ed:author')?></label></td>
                    <td><?php if (isset($btn->info['author']) === TRUE) echo $btn->info['author']?></td>
                    <td><label>URL</label></td>
                    <td><?php if (isset($btn->info['author_url']) === TRUE) echo $btn->info['author_url']?></td>
                </tr>
                <tr>
                    <td><label><?=lang('ed:desc')?></label></td>
                    <td colspan="3"><?php if (isset($btn->info['description']) === TRUE) echo $btn->info['description']?></td>
                </tr>
            </tbody>
        </table>

        <h2><?=lang('ed:btn_settings')?></h2>
        <div id="btn_<?=$btn_class?>">
            <?=$btn->display_settings($btn->settings)?>
        </div>
    </div>

    <input name="class" value="<?=$btn_class?>" type="hidden">
    <input name="submit" value="<?=lang('ed:save')?>" class="submit" type="submit">
    <?=form_close()?>

</div>
