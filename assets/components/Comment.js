import React from 'react'
import { Col, Row } from 'react-bootstrap'

const Comment = (props) => {
  const { item } = props

  return (
    <Row>
      <Col>{item.user}</Col>
      <Col lg={12}>
        <div>{item.text}</div>
      </Col>
    </Row>
  )
}

export default Comment
