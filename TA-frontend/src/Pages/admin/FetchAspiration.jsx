import { useEffect, useState } from "react";
import { getToken } from "../../utils/utils";
import { Link, useNavigate } from "react-router-dom";
import { ThreeDot } from "react-loading-indicators";

export default function FetchAspiration() {
    const [asp, setAsp] = useState([]);
    const [filterTarget, setFilterTarget] = useState("")
    const [loading, setLoading] = useState(true);
    const navigate = useNavigate()

    async function fetchAspiration() {
        setLoading(true);
        const res = await fetch("http://localhost:8000/api/aspirations", {
            headers: {
                "Authorization": `Bearer ${getToken()}`,
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            method: "GET",
        });
        const data = await res.json();
        setAsp(Object.values(data.Aspiration));
        console.log(Object.values(data.Aspiration));
        setLoading(false);
    }

    async function handleDelete(id) {
        const res = await fetch(`http://localhost:8000/api/aspirations/${id}`, {
            method: "DELETE",
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${getToken()}`,
                "Content-Type": "application/json"
            }
        })
        const data = await res.json()
        fetchAspiration()
    }

    useEffect(() => {
        fetchAspiration();
    }, []);

    const filteredAsp = asp.filter(item => {
        return filterTarget === "" || item.to === filterTarget;
    }).slice().sort((a, b) => b.date.localeCompare(a.date));

    return (
        <div className="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-4">
            <div className="flex justify-evenly items-center">
                <h1 className="text-2xl font-bold text-red-700 mb-6 text-center">
                    📜 Daftar Aspirasi
                </h1>
                <select
                    onChange={(e) => setFilterTarget(e.target.value)}
                    className="mb-6 bg-red-100 rounded-xs"
                >
                    <option value="">ALL</option>
                    <option value="MPK">MPK</option>
                    <option value="OSIS">OSIS</option>
                    <option value="SEKOLAH">SEKOLAH</option>
                </select>
            </div>

            <div className="flex flex-col gap-4 max-w-2xl mx-auto">
                {loading ? (
                    <div className="flex justify-center items-center min-h-[60vh]">
                        <ThreeDot color="#ff4747" size="medium" text="" textColor="#000000" />
                    </div>
                ) : filteredAsp.length > 0 ? (
                    filteredAsp.map((item, i) => (
                        <div
                            key={i}
                            className={`bg-white rounded-xl shadow-md p-5 border-l-8 hover:shadow-lg transition-shadow ${item.to == "MPK"
                                ? "border-red-600"
                                : item.to == "OSIS"
                                    ? "border-blue-500"
                                    : "border-gray-400"
                                }`}
                        >
                            <div className="flex justify-between items-start">
                                <div>
                                    <h3
                                        className={`text-lg font-semibold ${item.to == "MPK"
                                            ? "text-red-700"
                                            : item.to == "OSIS"
                                                ? "text-blue-700"
                                                : "text-black"
                                            }`}
                                    >
                                        🎯 {item.to}
                                    </h3>
                                    <p className="text-gray-700 mt-1">{item.message}</p>
                                    {item.date && (
                                        <p className="text-xs text-gray-500 mt-3">
                                            {new Date(item.date).toLocaleString("id-ID", {
                                                dateStyle: "long",
                                                timeStyle: "short",
                                            })}
                                        </p>
                                    )}
                                </div>

                                {/* Tombol delete */}
                                <button
                                    onClick={() => handleDelete(item.id)}
                                    className="ml-4 text-gray-500 px-3 py-1 rounded-lg text-sm hover:bg-gray-200 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                                    </svg>
                                </button>
                            </div>

                            {/* Link ke detail */}
                            <Link
                                to={`/home/aspirations/${item.id}`}
                                className="block text-blue-600 mt-3 text-sm hover:underline"
                            >
                                Lihat Detail →
                            </Link>
                        </div>
                    ))
                ) : (
                    <p className="text-center text-gray-500">🙁 Tidak ada aspirasi.</p>
                )}
            </div>


            <div className="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white">
                <Link className="" to="/home">Go back </Link>
            </div>
        </div>
    );

}
