import React, { useContext, useEffect, useState } from 'react'
import { Button, Col, Form, Row } from 'react-bootstrap'
import { Redirect, useLocation } from 'react-router-dom'
import { AuthContext } from '../context/AuthContext'

const { API_HOST } = process.env

const Login = () => {
  const user = useContext(AuthContext)
  const [validated, setValidated] = useState(false)
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const location = useLocation()

  const handleSubmit = (e) => {
    e.preventDefault()
    const form = e.currentTarget
    if (form.checkValidity() === false) {
      e.stopPropagation()
    }

    if (!user.token) {
      fetch(`${API_HOST}/api/auth/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username: email, password }),
      })
        .then((res) => {
          return res.json()
        })
        .then((data) => {
          user.setUser({ ...user, token: data.token })
        })
    }
    setValidated(true)
  }

  // eslint-disable-next-line consistent-return
  useEffect(() => {
    if (user) {
      return (
        <Redirect
          to={{
            pathname: '/',
            state: { from: location },
          }}
        />
      )
    }
  }, [user])

  return (
    <>
      <Row className="justify-content-center">
        <Col md={4} sm={12}>
          <Form noValidate validated={validated} onSubmit={handleSubmit}>
            <Form.Group>
              <Form.Label>Email</Form.Label>
              <Form.Control
                type="email"
                placeholder="Email"
                required
                value={email}
                onChange={(e) => {
                  e.preventDefault()
                  setEmail(e.currentTarget.value)
                }}
              />
              <Form.Control.Feedback type="invalid">
                Invalid email
              </Form.Control.Feedback>
            </Form.Group>
            <Form.Group>
              <Form.Label>Password</Form.Label>
              <Form.Control
                type="password"
                placeholder="Password"
                required
                value={password}
                onChange={(e) => {
                  e.preventDefault()
                  setPassword(e.currentTarget.value)
                }}
              />
              <Form.Control.Feedback type="invalid">
                Enter password
              </Form.Control.Feedback>
            </Form.Group>
            <Button variant="primary" type="submit">
              Login
            </Button>
          </Form>
        </Col>
      </Row>
    </>
  )
}

export default Login
