import React, { useContext, useState } from 'react'

const initial = {
  profile: null,
  token: null,
  loggedIn: false,
}

const AuthContext = React.createContext()

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(initial)
  const auth = { user, setUser }

  return <AuthContext.Provider value={auth}>{children}</AuthContext.Provider>
}

export const useAuth = () => {
  const context = useContext(AuthContext)
  if (context === undefined) {
    throw new Error('AuthProvider not found')
  }

  return context
}
