import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router } from 'react-router-dom';
import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import Home from './components/home';

ReactDOM.render(<Router><Home /></Router>, document.getElementById('root'));
