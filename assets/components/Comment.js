import React from 'react'
import { Col, Dropdown, Row } from 'react-bootstrap'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faUserCircle } from '@fortawesome/free-solid-svg-icons'

const Comment = (props) => {
  const { item, user, handleDelete, handleEdit } = props
  const options = {
    day: 'numeric',
    month: 'long',
  }

  const handleSelect = (key) => {
    if (key === '2') {
      handleDelete(item.id)
    } else {
      handleEdit(item.id, item.text)
    }
  }

  return (
    <Row className="align-items-center">
      <Col lg={12} className="p-2">
        {item.text}
        {user.profile?.id === item.user.id && (
          <Dropdown className="ml-2 comment-dropdown" onSelect={handleSelect}>
            <Dropdown.Toggle className="comment-toggle" />
            <Dropdown.Menu>
              <Dropdown.Item eventKey="1">Edit</Dropdown.Item>
              <Dropdown.Item eventKey="2">Delete</Dropdown.Item>
            </Dropdown.Menu>
          </Dropdown>
        )}
      </Col>
      <Col xs={2} className="p-2">
        <FontAwesomeIcon icon={faUserCircle} size="2x" />
      </Col>
      <Col xs={10} className="p-2 comment-owner">{`${item.user.name} ${
        item.user.last_name
      } ${new Intl.DateTimeFormat({}, options).format(
        new Date(item.createdAt),
      )}`}</Col>
    </Row>
  )
}

export default Comment
