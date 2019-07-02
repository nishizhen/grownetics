<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="sidebar"  class="nav-collapse collapsed">
	<ul class="sidebar-menu" id="nav-accordion">
		<li class="mt">
			<a href="/"<?php if ($this->request->params['controller']=='Dash'&&$this->request->params['action']=='index'){?> class='active'<?php }?>>
				<i class="fa fa-home"></i>
				<span>Ceres Dashboard</span>
			</a>
		</li>
		<li class="sub-menu">
			<a href="/wikis/view/home"<?php if ($this->request->params['controller']=='wikis'){?> class='active'<?php }?>>
				<i class="fa fa-info"></i>
				<span>Wiki</span>
			</a>
		</li>
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
