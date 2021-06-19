import React, {Component} from 'react';
import { Container, Navbar } from 'react-bootstrap'

const Home = (props) => {
  return(
    <Container style={{minHeight: '100vh'}}>
      <Navbar bg="light" expand="lg">
        <Navbar.Brand href="#home">Test app</Navbar.Brand>
      </Navbar>
      <Container>

      </Container>
    </Container>
  )
}

export default Home
