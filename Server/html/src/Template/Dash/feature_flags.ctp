<div class="row mt">
    <div class="col-md-12">
        <div class="content-panel">
            <h4><i class="fa fa-angle-right"></i> Feature Flags</h4><hr><table class="table table-striped table-advance table-hover">


                <thead>
                <tr>
                    <th><i class="fa fa-flag"></i> Flag</th>
                    <th><i class=" fa fa-edit"></i> Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $this->Form->resetTemplates();
                foreach ($flags as $flag) {
                    $flagName = preg_split('#/#',$flag->Key)[1]; ?>
                    <tr>
                        <td><?=$flagName?></td>
                        <td><?=$this->FeatureFlags->getStatusBadge($flagName)?></td>
                        <td>
                            <?=$this->FeatureFlags->getToggleLink($flagName)?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div><!-- /content-panel -->
    </div><!-- /col-md-12 -->
</div>

