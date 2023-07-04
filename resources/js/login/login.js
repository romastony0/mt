Ext.require(['*']);
Ext.onReady(function(){
    Ext.create('Ext.Viewport', {
        layout: {
            type: 'border',
            padding: 0
        },
        defaults: {
            split: true,
            collapsible: false,
            title: false,
            split: false,
            border: false
        },
        items: [{
            region: 'center',
            layout: 'fit',
            bodyStyle: 'background: #FFFFFF url(../resources/images/others/login-bg.png) left bottom no-repeat;',
			html:'<div style="height: 100%; background: transparent url(../resources/images/others/logo-large.png) center 10% no-repeat;"></div>'
        }]
    });
    //username validation vtype
    var usernameValTest = /^[a-zA-Z][-_.a-zA-Z0-9]{0,30}$/;
    Ext.apply(Ext.form.VTypes, {
        usernameVal: function(val){
            return usernameValTest.test(val);
        },
        usernameValText: 'Username not valid! Must be alphanumeric".',
        usernameValMask: /[-_.a-zA-Z0-9]/
    });
    
    //password validation vtype
    var passwordValTest = /^.{6,31}$/;
    Ext.apply(Ext.form.VTypes, {
        passwordVal: function(val, field){
            return passwordValTest.test(val);
        },
        passwordValText: 'Password length must be 6 to 31 characters long".',
        passwordValMask: /./
    });
    
    var userAddFormPanel = Ext.create('Ext.form.Panel', {
        border: false,
        height: 130,
        bodyStyle: 'background-color: transparent;',
        defaults: {
            width: 234
        },
        items: [{
	            border: false,
	            html: '<br />'
        }, {
	            xtype: 'textfield',
	            name: 'username',
	            id: 'username',
	            emptyText: 'Username',
	            allowBlank: false,
	            vtype: 'usernameVal'
        }, {
	            border: false,
	            html: '<br />'
        }, {
	            xtype: 'textfield',
	            name: 'password',
	            inputType: 'password',
	            emptyText: 'Password',
	            allowBlank: false,
	            vtype: 'passwordVal',
	    		listeners : {
	    			specialkey: function(field, e){
	    				if (e.getKey() == e.ENTER) { login(); }
	                }
	    		}
        }],
        buttons: [{
            text: '<b>Login</b>',
            formBind: true,
            icon: '../resources/images/others/key.png',
            scope: this,
            handler: login
        }, {
            text: '<b>Reset</b>',
            icon: '../resources/images/others/reset.png',
            handler: function(){
                var formAdd = userAddFormPanel.getForm();
                formAdd.reset();
                Ext.getCmp('username').focus(false, 200);
            }
        }]
    });
    var loginWindow = Ext.create('Ext.window.Window', {
        title: false,
        width: 450,
        height: 200,
        id: 'userlogin',
        closable: false,
        draggable: false,
        resizable: false,
        frame: false,
        border: true,
        labelWidth: 0,
        bodyStyle: 'padding: 20px; padding-left: 180px; background: #FFFFFF url(../resources/images/others/login.png) 20px center no-repeat;',
        items: userAddFormPanel
    });
    loginWindow.show();
    
    function login(){
    	var formAdd = userAddFormPanel.getForm();
        if (formAdd.isValid()) {
        	var windowMask = new Ext.LoadMask(Ext.getCmp('userlogin'), { useMsg : false});
        	windowMask.show();
        	formAdd.submit({
        		url: 'action?application=user&action=userlogin',
                failure: function(formAdd, action){
                	windowMask.hide();
                    Ext.Msg.show({
                        title: 'Login Failed!',
                        buttons: Ext.Msg.OK,
                        msg: 'The given credentials are not matching or Invalid! Please try again',
                        width: 350,
                        minWidth: 350,
                        icon: Ext.MessageBox.INFO
                    });
                },
                success: function(formAdd, action){
				    alert('insidefunction');
                	window.location = 'home.php';
                 
                }
            });
        }
    }
});

