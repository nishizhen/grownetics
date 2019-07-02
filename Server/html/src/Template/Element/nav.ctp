<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="sidebar"  class="nav-collapse ">
  <ul class="sidebar-menu" id="nav-accordion">
    <p class="centered"><a href="/users/account"><img src="https://www.gravatar.com/avatar/<?php echo md5( strtolower( trim( $this->request->session()->read('Auth.User.email') ) ) );
			?>?d=identicon" class="img-circle" width="60" height="60"></a></p>
    <h5 class="centered"><a href="/users/account" id="accountLink" data-userid='<?=$this->request->session()->read('Auth.User.id')?>'><?=$this->request->session()->read('Auth.User.name')?></a></h5>
    <li class="mt">
      <a href="/"<?php if ($this->request->params['controller']=='Dash'&&$this->request->params['action']=='index'){?> class='active'<?php }?>>
      <i class="fa fa-home"></i>
      <span>Dashboard</span>
      </a>
    </li>
    <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "harvestbatches", "index"))) { ?>
    <li class="sub-menu">
      <a href="javascript:;"<?php if ($this->request->params['controller']=='HarvestBatches' || $this->request->params['controller']=='Recipes') {?> class='active'<?php }?>>
      <i class="fa fa-leaf"></i>
      <span>Batches</span>
      </a>
      <ul class="sub">
        <li><a href="/harvest-batches/">Active Batches</a></li>
        <li><a href="/harvest-batches/archive/">Batches Archive</a></li>
        <li><a href="/recipes/">Recipes</a></li>
      </ul>
    </li>
    <?php } ?>
    <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "tasks", "index"))) { ?>
    <li class="sub-menu">
      <a href="/tasks"<?php if ($this->request->params['controller']=='Tasks'){?> class='active'<?php }?>>
      <i class="fa fa-list"></i>
      <span>Workflow</span>
      </a>
    </li>
    <?php } ?>
    <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "cultivars", "index"))) { ?>
    <li class="sub-menu">
      <a href="/cultivars"<?php if ($this->request->params['controller']=='Cultivars'){?> class='active'<?php }?>>
      <i class="fa fa-pagelines"></i>
      <span>Cultivars</span>
      </a>
    </li>
    <?php } ?>
    <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "notes", "index"))) { ?>
    <li class="sub-menu">
      <a href="/notes"<?php if ($this->request->params['controller']=='Notes'){?> class='active'<?php }?>>
      <i class="fa fa-camera"></i>
      <span>Notes &amp; Photos</span>
      </a>
    </li>
    <?php } ?>
    <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "charts", "index"))) { ?>
      <li class="sub-menu">
      <a href="javascript:;"<?php if ($this->request->params['controller']=='Charts'){?> class='active'<?php }?>>
      <i class="fa fa-area-chart"></i>
      <span>Charts</span>
      </a>
        <ul class="sub">
            <li><a href="/charts/">Zone</a></li>
            <li><a href="/charts/harvestBatchView">Batch</a></li>
            <li><a href="/grafana/">Grafana</a></li>
        </ul>

    </li>
    <?php } ?>
    <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "outputs", "index"))) { ?>
    <li class="sub-menu">
      <a href="/outputs"<?php if ($this->request->params['controller']=='Outputs'){?> class='active'<?php }?>>
      <i class="fa fa-lightbulb-o"></i>
      <span>Hardware</span>
      </a>
    </li>
    <?php } ?>
    <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "wikis", "view"))) { ?>
    <li class="sub-menu">
      <a href="/wikis/"<?php if ($this->request->params['controller']=='Wikis'){?> class='active'<?php }?>>
      <i class="fa fa-info"></i>
      <span>Ops Wiki</span>
      </a>
    </li>
    <?php } ?>
    <li class="sub-menu">
      <a href="javascript:;"<?php if ($this->request->params['controller']=='Devices' || $this->request->params['controller']=='Zones' || $this->request->params['controller']=='Rules' || $this->request->params['controller']=='Notifications' || $this->request->params['controller']=='Users'){?> class='active'<?php }?>>
        <i class="fa fa-cog"></i>
        <span>Settings</span>
      </a>
      <ul class="sub">
        <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "devices", "index"))) { ?>
        <li class="">
          <a href="/devices">
          <span>Devices</span>
          </a>
        </li>
        <?php } ?>
        <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "zones", "index"))) { ?>
        <li class="">
          <a href="/zones">
          <span>Zones</span>
          </a>
        </li>
        <?php } ?>
          <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "users", "index"))) { ?>
              <li><a href="/users/">Manage Users</a></li>
          <?php } ?>


        <?php if ((isset($role) && $role == 'Admin') || ($this->Acl->hasAccess($acls, "rules", "index"))) { ?>
        <li class="">
          <a href="/rules">
          <span>Rules</span>
          </a>
        </li>
        <li><a href="/notifications">System Log</a></li>
      </ul>
    </li>
    <?php } ?>

    <?php if (isset($role) && $role == 'Admin') { ?>
    <li class="sub-menu">
      <a href="javascript:;" <?php if ($this->request->params['controller']=='Users' || $this->request->params['controller']=='Acls' || $this->request->params['controller']=='Roles' || $this->request->params['controller']=='Facilities' || $this->request->params['controller']=='Api' || $this->request->params['controller']=='SensorTypes' || $this->request->params['action']=='system' || $this->request->params['action']=='server'){?> class='active'<?php }?>>
        <i class="fa fa-cog"></i>
        <span>Admin</span>
      </a>
      <ul class="sub">
        <li><a href="/acls/">ACLs</a></li>
        <li><a href="/roles/">Roles</a></li>
        <li><a href="/floorplans/">Floorplans</a></li>
        <li><a href="/facilities"<?php if ($this->request->params['controller']=='facilities'){?> class='active'<?php }?>>
          <span>Facilities</span>
          </a></li>
        <li><a href="/api/test/">API Test</a></li>
        <li><a href="/dash/server/">Server Information</a></li>
        <li><a href="/dash/status/">System Status</a></li>
        <li><a href="/dash/featureFlags/">Feature Flags</a></li>
        <li><a href="/dash/backups/">Backup Importer</a></li>
      </ul>
    </li>
    <?php } ?>
  </ul>
</div>
