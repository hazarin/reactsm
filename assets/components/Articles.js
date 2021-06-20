import React, { useEffect, useState } from 'react'
import { Col, NavLink, Row } from 'react-bootstrap'
import { Link } from 'react-router-dom'

// eslint-disable-next-line prefer-destructuring
const API_HOST = process.env.API_HOST

const Articles = () => {
  const [articles, setArticles] = useState(null)

  useEffect(() => {
    if (!articles) {
      fetch(`${API_HOST}/api/article/`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then((res) => {
          return res.json()
        })
        .then((data) => {
          setArticles(data)
        })
    }
  }, [articles])

  return (
    <Row>
      {Boolean(articles) &&
        articles.map((item) => (
          <Col sm={12} md={6} key={item.id} className="text-center">
            <h2 className="h2">
              <NavLink
                as={Link}
                to={`/article/${item.id}`}
                className="article-link"
              >
                {item.title}
              </NavLink>
            </h2>
          </Col>
        ))}
    </Row>
  )
}

export default Articles
