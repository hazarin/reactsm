import React from 'react'
import { Container, Nav, Navbar } from 'react-bootstrap'
import { Switch, Route, Link } from 'react-router-dom'
import Articles from './Articles'
import Login from './Login'
import Article from './Article'

const Home = () => {
  return (
    <Container style={{ minHeight: '100vh' }}>
      <Navbar bg="light" expand="lg">
        <Navbar.Brand as={Link} to="/">
          Test app
        </Navbar.Brand>
        <Nav className="ml-auto">
          <Nav.Link as={Link} to="/">
            Home
          </Nav.Link>
          <Nav.Link as={Link} to="/login">
            Login
          </Nav.Link>
        </Nav>
      </Navbar>
      <Container className="content-container">
        <Switch>
          <Route exact path="/">
            <Articles />
          </Route>
          <Route exact path="/login">
            <Login />
          </Route>
          <Route path="/article/:articleId">
            <Article />
          </Route>
          <Route render={() => <p>Not found</p>} />
        </Switch>
      </Container>
    </Container>
  )
}

export default Home
