<?php
return [
    'inputContainer' => '<div class="form-group">{{content}}</div>',
    'label' => '<label class="col-sm-2 col-sm-2 control-label"{{attrs}}>{{text}}</label>',
    'input' => '<div class="col-sm-10"><input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>{{icon}}</div>',
    'formStart' => '<div class="col-lg-12"><div class="form-panel"><h4 class="mb"><i class="fa fa-angle-right"></i> {{header}}</h4><form class="form-horizontal style-form"{{attrs}}>',
	'formEnd' => '</form></div></div></div>',
    'inputSubmit' => '<input type="{{type}}" class="btn btn-theme submitBtn"{{attrs}}/>',
    'select' => '<div class="col-sm-10"><select name="{{name}}" class="form-control"{{attrs}}>{{content}}</select></div>',
    'button' => '<button class="btn-theme btn submitBtn"{{attrs}}>{{text}}</button>',
];
