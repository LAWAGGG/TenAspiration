import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import './App.css'
import { Route, Routes } from 'react-router-dom'
import Aspiration from './Pages/user/Aspiration'
import Login from './Pages/auth/login'
import FetchAspiration from './Pages/admin/FetchAspiration'
import AspirationDetail from './Pages/admin/AspirationDetail'
import MainPage from './Pages/admin/MainPage'
import EventAspiration from './Pages/admin/Event/EventAspiration'
import AspirationEvent from './Pages/user/AspirationEvent'

function App() {
  return (
    <>
      <Routes>
        <Route path="/login" element={<Login></Login>}></Route>
        <Route path='/home' element={<MainPage></MainPage>}></Route>

        {/* Route For Aspiration Form (Event and daily) */}
        <Route path="/" element={<Aspiration></Aspiration>}></Route>
        <Route path='/event' element={<AspirationEvent></AspirationEvent>}></Route>

        {/* Route For Daily Aspirations */}
        <Route path="/home/aspirations" element={<FetchAspiration></FetchAspiration>}></Route>
        <Route path='/home/aspirations/:id' element={<AspirationDetail></AspirationDetail>}></Route>

        {/* Route For Event Aspirations */}
        <Route path='/home/aspirations/events/:id' element={<EventAspiration></EventAspiration>}></Route>
      </Routes>
    </>
  )
}

export default App
