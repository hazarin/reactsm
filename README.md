# Test app

### Docker deploy
`git clone git@github.com:hazarin/reactsm.git`   
`cd reactsm`   
`docker-compose up -d`

After that application available at https://127.0.0.1:8000/

During build user & article fixtures applied to database

Users:   
login: one@one.com password: User1   
login: two@two.com password: User2   
Admin:
login: admin@admin.com password: Admin

Backend API doc https://127.0.0.1:8000/api/doc
User login on SPA not permanent. It's uses state in React context.
It's not the main task in test, as I understand
