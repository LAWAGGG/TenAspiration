import { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { getToken } from "../../utils/utils";
import { ThreeDot } from "react-loading-indicators";

export default function MainPage() {
    const [event, setEvent] = useState([]);
    const [loading, setLoading] = useState(true);
    const navigate = useNavigate

    async function fetchEvent() {
        const res = await fetch("http://localhost:8000/api/events", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": `Bearer ${getToken()}`
            }
        });
        const data = await res.json();
        setEvent(data.Events || []);
        console.log(data.Events);
        setLoading(false);
    }

    async function handleDelete(id) {
        const res = await fetch(`http://localhost:8000/api/events/${id}`, {
            method: "DELETE",
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${getToken()}`,
                "Content-Type": "application/json"
            }
        })
        const data = await res.json()
        fetchEvent()
    }

    async function handleLogout(e) {
        e.preventDefault();
        const res = await fetch("http://localhost:8000/api/logout", {
            headers: {
                "Authorization": `Bearer ${getToken()}`,
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            method: "POST",
        });
        const data = await res.json()

        if (res.status == 200) {
            navigate("/")
        }

        console.log(data)
    }

    useEffect(() => {
        fetchEvent();
    }, []);

    return (
        <div className="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-6">
            <div className="max-w-5xl mx-auto pb-5">
                {/* Header */}
                <div className="text-center mb-10">
                    <h1 className="text-3xl font-bold text-red-700">
                        Admin Dashboard
                    </h1>
                    <p className="mt-3 text-gray-600">
                        Kelola aspirasi dan event sekolah dengan mudah.
                    </p>
                </div>

                {/* Shortcut */}
                <div className="flex justify-center mb-8">
                    <Link
                        to="/home/aspirations"
                        className="px-5 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition"
                    >
                        📜 Lihat Aspirasi Harian
                    </Link>
                </div>

                {/* Event List */}
                <div>
                    <h2 className="text-xl font-semibold text-gray-800 mb-6">
                        📅 Daftar Event
                    </h2>
                    {
                        loading ? (
                            <div className="flex justify-center items-center min-h-[50vh]">
                                <ThreeDot color="#ff4747" size="medium" text="" textColor="#000000" />
                            </div>) :
                            event.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                    {event.map((item, i) => (
                                        <div
                                            key={i}
                                            className="p-5 bg-white rounded-xl shadow-md border-l-8 border-red-400 hover:shadow-lg transition"
                                        >
                                            <h3 className="text-lg font-bold text-red-700 mb-2">
                                                {item.name}
                                            </h3>
                                            <p className="text-gray-700 mb-4">
                                                {item.description || "Tidak ada deskripsi"}
                                            </p>
                                            <Link
                                                to={`/home/aspirations/events/${item.id}`}
                                                className="inline-block px-4 py-1 bg-red-200 text-gray-700 rounded-lg hover:bg-red-300 transition"
                                            >
                                                Lihat Aspirasi →
                                            </Link>
                                            <button
                                                onClick={() => handleDelete(item.id)}
                                                className="ml-4 text-gray-500 px-3 py-1 rounded-lg text-sm hover:bg-gray-200 cursor-pointer"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" className="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                    <path d="M11 1.5v1h3.5a.5.5 ..." />
                                                </svg>
                                            </button>

                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-center text-gray-500">
                                    🙁 Belum ada event yang tersedia.
                                </p>
                            )}

                    <div className="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white cursor-pointer hover:bg-red-600">
                        <button className="cursor-pointer hover:bg-red-600" onClick={(e) => handleLogout(e)}>Logout</button>
                    </div>
                </div>
            </div>
        </div>
    );
}
