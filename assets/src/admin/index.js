import 'react-hot-loader/patch';
import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import './styles/style.scss';
import menuFix from './utils/admin-menu-fix';
import { BrowserRouter } from 'react-router-dom';

var mountNode = document.getElementById('hmr-app');
ReactDOM.render(
  <BrowserRouter>
    <App />
  </BrowserRouter>,
  mountNode
);

// fix the admin menu for the slug "vue-app"
menuFix('dc-bkash');
