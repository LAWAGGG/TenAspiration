import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import './App.css'
import { Route, Routes } from 'react-router-dom'
import Aspiration from './Pages/user/Aspiration'
import Login from './Pages/auth/login'
import FetchAspiration from './Pages/admin/FetchAspiration'
import AspirationDetail from './Pages/admin/AspirationDetail'

function App() {
  return (
    <>
      <Routes>
        <Route path="/" element={<Aspiration></Aspiration>}></Route>
        <Route path="/login" element={<Login></Login>}></Route>
        <Route path="/aspirations" element={<FetchAspiration></FetchAspiration>}></Route>
        <Route path='/aspirations/:id' element={<AspirationDetail></AspirationDetail>}></Route>
      </Routes>
    </>
  )
}

export default App
