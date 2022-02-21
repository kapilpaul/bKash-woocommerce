import 'react-hot-loader/patch';
import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import './styles/style.scss';

var mountNode = document.getElementById( 'dc-bkash-upgrade-notice-container' );
ReactDOM.render( <App />, mountNode );
