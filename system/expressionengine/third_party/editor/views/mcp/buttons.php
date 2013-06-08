<?php echo $this->view('menu'); ?>
<div class="dtable" style="margin:0">
    <h2 style="padding:9px 9px 5px">
        <?=lang('ed:buttons')?>
    </h2>
</div>

<div id="dbody">

    <div style="padding:10px 20px">
        <table cellpadding="0" cellspacing="0" border="0" class="DFTable" style="width:80%">
            <thead>
                <tr>
                    <th>Class</th>
                    <th><?=lang('ed:label')?></th>
                    <th><?=lang('ed:version')?></th>
                    <th><?=lang('ed:desc')?></th>
                    <th><?=lang('ed:author')?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($this->editor->buttons) == TRUE):?>
                <tr>
                    <td colspan="5"><?=lang('ed:no_editors')?></td>
                </tr>
                <?php endif;?>

                <?php foreach($this->editor->buttons as $class => $btn):?>
                <tr>
                    <td><?=$class?></td>
                    <td><?=$btn->info['name']?></td>
                    <td><?php if (isset($btn->info['version']) === TRUE) echo $btn->info['version']?></td>
                    <td><?php if (isset($btn->info['description']) === TRUE) echo $btn->info['description']?></td>
                    <td>
                        <?php if (isset($btn->info['author']) === TRUE)
                        {
                            if (isset($btn->info['author_url']) === TRUE)
                            {
                                echo '<a href="'.$btn->info['author_url'].'">'.$btn->info['author'].'</a>';
                            }
                            else
                            {
                                echo $btn->info['author'];
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php if (isset($btn->info['settings']) === TRUE && $btn->info['settings'] == TRUE):?>
                        <a href="<?=$base_url?>&method=buttons_settings&class=<?=$class?>"><?=lang('ed:def_settings')?></a>
                        <?php endif;?>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>

</div>
