import React, { useEffect, useState } from 'react'
import { Button, Col, Modal, Row } from 'react-bootstrap'
import { useParams } from 'react-router-dom'
import CommentForm from './CommentForm'
import Comment from './Comment'
import { useAuth } from '../context/AuthContext'

// eslint-disable-next-line prefer-destructuring
const API_HOST = process.env.API_HOST

const Article = () => {
  const { articleId } = useParams()
  const [article, setArticle] = useState({})
  const [comment, setComment] = useState('')
  const [commentId, setCommentId] = useState(null)
  const [showModal, setShowModal] = useState(false)
  const { user } = useAuth()

  const handleSubmit = async (e, id) => {
    e.preventDefault()
    let res

    if (id) {
      res = await fetch(`${API_HOST}/api/comment/${id}/`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${user.token}`,
        },
        body: JSON.stringify({ text: comment }),
      })
    } else {
      res = await fetch(`${API_HOST}/api/article/${articleId}/comment/`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${user.token}`,
        },
        body: JSON.stringify({ text: comment }),
      })
    }
    const data = await res.json()
    if (res.ok) {
      const newArticle = { ...article }
      if (id) {
        newArticle.comments = newArticle.comments.map((item) => {
          if (item.id === id) {
            return data
          }
          return item
        })
      } else {
        newArticle.comments.push(data)
      }
      setComment('')
      setArticle(newArticle)
    }
  }

  const handleDelete = (id) => {
    setShowModal(id)
  }

  const deleteComment = async () => {
    const res = await fetch(`${API_HOST}/api/comment/${showModal}/`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${user.token}`,
      },
    })
    if (res.ok) {
      const id = showModal
      setShowModal(false)
      const newArticle = { ...article }
      newArticle.comments = newArticle.comments.filter((item) => item.id !== id)
      setArticle(newArticle)
    }
  }

  const handleEdit = (id, val) => {
    setCommentId(id)
    setComment(val)
  }

  const handleCancel = () => {
    setCommentId(null)
    setComment('')
  }

  useEffect(() => {
    if (articleId) {
      fetch(`${API_HOST}/api/article/${articleId}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((res) => {
          return res.json()
        })
        .then((data) => {
          setArticle(data)
        })
    }
  }, [articleId])

  return (
    <>
      <Modal show={Boolean(showModal)} backdrop="static" keyboard={false}>
        <Modal.Body>Are You sure, You want to delete comment?</Modal.Body>
        <Modal.Footer>
          <Button variant="danger" onClick={() => deleteComment()}>
            Yes
          </Button>
          <Button variant="secondary" onClick={() => setShowModal(false)}>
            No
          </Button>
        </Modal.Footer>
      </Modal>
      <Row className="p-2">
        <Col lg={12}>
          <h1>{article?.title}</h1>
        </Col>
      </Row>
      <Row className="p-2">
        <Col lg={12}>
          <div>{article?.content}</div>
        </Col>
      </Row>
      {user.loggedIn && (
        <Row className="p-2">
          <Col lg={6}>
            <CommentForm
              comment={comment}
              setComment={setComment}
              commentId={commentId}
              handleSubmit={handleSubmit}
              handleCancel={handleCancel}
            />
          </Col>
        </Row>
      )}
      <Row className="p-2">
        <Col lg={6}>
          {Boolean(article?.comments) &&
            article.comments.map((item) => (
              <Comment
                key={item.id}
                item={item}
                user={user}
                handleDelete={handleDelete}
                handleEdit={handleEdit}
              />
            ))}
        </Col>
      </Row>
    </>
  )
}

export default Article
