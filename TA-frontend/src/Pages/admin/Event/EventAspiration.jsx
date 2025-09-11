import { useEffect, useState } from "react"
import { Link, useParams } from "react-router-dom"
import { getToken } from "../../../utils/utils"
import { ThreeDot } from "react-loading-indicators"

export default function EventAspiration() {
    const [event, setEvent] = useState([])
    const [loading, setLoading] = useState(true)
    const params = useParams()

    async function fetchAspirationEvent() {
        const res = await fetch(`http://localhost:8000/api/aspiration/events/event/${params.id}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": `Bearer ${getToken()}`
            }
        })
        const data = await res.json()
        setEvent(Object.values(data.Aspirations))
        console.log(Object.values(data.Aspirations));
        setLoading(false)
    }

    useEffect(() => {
        fetchAspirationEvent()
    }, [])

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 p-4">
                <div className="flex justify-center items-center min-h-[60vh]">
                        <ThreeDot color="#ff4747" size="medium" text="" textColor="#000000" />
                    </div>
            </div>
        )
    }

    return (
        <div className="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 p-4 md:p-8">
            <div className="max-w-4xl mx-auto">
                <div className="mb-8 text-center">
                    <h1 className="text-3xl font-bold text-gray-800 mb-2">
                        Aspirasi <span className="text-red-600">Event</span>
                    </h1>
                    <p className="text-gray-600">Daftar aspirasi yang telah dikumpulkan</p>
                </div>

                {event.length === 0 ? (
                    <div className="bg-white rounded-xl shadow-md p-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 className="text-xl font-semibold text-gray-700 mb-2">Belum ada aspirasi</h3>
                        <p className="text-gray-500">Belum ada aspirasi yang dikirim untuk event ini.</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {event.map((item, i) => (
                            <div className="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500 hover:shadow-lg transition-all duration-300" key={i}>
                                <div className="p-6">
                                    <div className="flex items-start mb-4">
                                        <div className="bg-red-100 p-2 rounded-full mr-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                            </svg>
                                        </div>
                                        <div className="flex-1">
                                            <p className="text-gray-800 font-medium">{item.message}</p>
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                        <div className="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 text-red-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <span className="text-sm font-medium text-gray-700">
                                                {item.to === "lainnya" ? item.other_to : item.to}
                                            </span>
                                        </div>

                                        {item.date && (
                                            <div className="flex items-center text-xs text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {new Date(item.date).toLocaleString("id-ID", {
                                                    dateStyle: "long",
                                                    timeStyle: "short",
                                                })}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
            <div className="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white">
                <Link className="" to="/home">Go back </Link>
            </div>
        </div>
    )
}