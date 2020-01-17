/**
 * 企业注册页面
 */
import React from 'react';
import ReactDOM from 'react-dom';
import InviteStaff from './pages/InviteStaff';

if (document.getElementById('invite-staff')) {

  ReactDOM.render(<InviteStaff />, document.getElementById('invite-staff'));

}
