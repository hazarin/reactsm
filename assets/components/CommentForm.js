import React, { useEffect, useState } from 'react'
import { Button, Form } from 'react-bootstrap'

const CommentForm = (props) => {
  const { comment, commentId, setComment, handleSubmit, handleCancel } = props
  const [showButtons, setShowButtons] = useState(false)

  useEffect(() => {
    setShowButtons(Boolean(comment))
  }, [comment])

  return (
    <Form noValidate onSubmit={(e) => handleSubmit(e, commentId)}>
      <Form.Group>
        <Form.Control
          type="text"
          placeholder="Comment"
          required
          commentid={commentId}
          name="text"
          value={comment}
          onChange={(e) => {
            if (e.currentTarget.value) {
              setShowButtons(true)
            } else {
              setShowButtons(false)
            }
            setComment(e.currentTarget.value)
          }}
        />
        <Form.Control.Feedback type="invalid">
          Text can not be empty
        </Form.Control.Feedback>
      </Form.Group>
      {showButtons && (
        <>
          <Button
            variant="secondary"
            onClick={() => handleCancel()}
            type="reset"
            className="mr-2"
          >
            Cancel
          </Button>
          <Button variant="primary" type="submit">
            Send
          </Button>
        </>
      )}
    </Form>
  )
}

export default CommentForm
