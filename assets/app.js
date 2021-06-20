import React, { useState } from 'react'
import ReactDOM from 'react-dom'
import { BrowserRouter as Router } from 'react-router-dom'
import Home from './components/Home'
import { AuthProvider } from './context/AuthContext'
import './styles/app.css'
import 'bootstrap/dist/css/bootstrap.min.css'

const App = () => {
  return (
    <Router>
      <AuthProvider>
        <Home />
      </AuthProvider>
    </Router>
  )
}

ReactDOM.render(<App />, document.getElementById('root'))
