import React, { useEffect, useState } from 'react'
import { Col, Row } from 'react-bootstrap'
import { useParams } from 'react-router-dom'
import Comment from './Comment'

// eslint-disable-next-line prefer-destructuring
const API_HOST = process.env.API_HOST

const Article = () => {
  const { articleId } = useParams()
  const [article, setArticle] = useState({})

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
    <Row>
      <Col lg={12}>
        <h1>{article?.title}</h1>
      </Col>
      <Col lg={12}>
        <div>{article?.content}</div>
      </Col>
      <Col>
        {Boolean(article?.comments) &&
          article.comments.map((item) => <Comment key={item.id} item={item} />)}
      </Col>
    </Row>
  )
}

export default Article
