import { useEffect, useState } from "react";
import { getToken } from "../../utils/utils";
import { Link } from "react-router-dom";
import { ThreeDot } from "react-loading-indicators";

export default function FetchAspiration() {
    const [asp, setAsp] = useState([]);
    const [filterTarget, setFilterTarget] = useState("");
    const [loading, setLoading] = useState(true);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteId, setDeleteId] = useState(null);

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
        setLoading(false);
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    async function handleDelete() {
        if (!deleteId) return;
        await fetch(`http://localhost:8000/api/aspirations/${deleteId}`, {
            method: "DELETE",
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${getToken()}`,
                "Content-Type": "application/json"
            }
        });
        setShowDeleteModal(false);
        setDeleteId(null);
        fetchAspiration();
    }

    async function handleCsvAspiration() {
        const res = await fetch(`http://localhost:8000/api/aspirations/csv`, {
            method: 'GET',
            headers: {
                "Accept": "text/csv",
                "Authorization": `Bearer ${getToken()}`
            }
        });
        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = `aspiration.csv`;
        document.body.appendChild(a);
        a.click();
        a.remove();

        window.URL.revokeObjectURL(url);
    }

    useEffect(() => {
        fetchAspiration();
    }, []);

    const filteredAsp = asp
        .filter(item => filterTarget === "" || item.to === filterTarget)
        .slice()
        .sort((a, b) => b.date.localeCompare(a.date));

    return (
        <div className="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-4">
            {/* Header */}
            <div className="max-w-4xl mx-auto mb-8">
                <div className="flex flex-col md:flex-row items-center justify-between gap-4">
                    <h1 className="text-2xl font-bold text-red-700 text-center md:text-left">
                        📜 Daftar Aspirasi
                    </h1>

                    <div className="flex items-center gap-3">
                        <select
                            onChange={(e) => setFilterTarget(e.target.value)}
                            className="px-3 py-2 rounded-md bg-red-100 text-sm border border-red-200 focus:outline-none focus:ring-2 focus:ring-red-400"
                        >
                            <option value="">ALL</option>
                            <option value="MPK">MPK</option>
                            <option value="OSIS">OSIS</option>
                            <option value="SEKOLAH">SEKOLAH</option>
                        </select>

                        <button
                            onClick={handleCsvAspiration}
                            className="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition text-sm flex items-center gap-2"
                        >
                            📥 <span>Export CSV</span>
                        </button>
                    </div>
                </div>
            </div>

            {/* Content */}
            <div className="flex flex-col gap-4 max-w-2xl mx-auto">
                {loading ? (
                    <div className="flex justify-center items-center min-h-[60vh]">
                        <ThreeDot color="#ff4747" size="medium" text="" textColor="#000000" />
                    </div>
                ) : filteredAsp.length > 0 ? (
                    filteredAsp.map((item, i) => (
                        <div
                            key={i}
                            className={`bg-white rounded-xl shadow-md p-5 border-l-8 hover:shadow-lg transition-shadow ${item.to === "MPK"
                                ? "border-red-600"
                                : item.to === "OSIS"
                                    ? "border-blue-500"
                                    : "border-gray-400"
                                }`}
                        >
                            <div className="flex justify-between items-start">
                                <div>
                                    <h3
                                        className={`text-lg font-semibold ${item.to === "MPK"
                                            ? "text-red-700"
                                            : item.to === "OSIS"
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

                                <button
                                    onClick={() => confirmDelete(item.id)}
                                    className="ml-4 text-gray-500 px-3 py-1 rounded-lg text-sm hover:bg-gray-200 cursor-pointer"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-trash3-fill" viewBox="0 0 16 16">
                                        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                                    </svg>
                                </button>
                            </div>

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

            {/* Delete Confirmation Modal */}
            {showDeleteModal && (
                <div className="fixed inset-0 bg-opacity-75 flex justify-center items-center z-50 backdrop-blur-sm">
                    <div className="bg-white rounded-xl shadow-lg p-6 w-96 text-center">
                        <h2 className="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Hapus</h2>
                        <p className="text-gray-600 mb-6">Apakah kamu yakin ingin menghapus aspirasi ini? Tindakan ini tidak bisa dibatalkan.</p>
                        <div className="flex justify-center gap-4">
                            <button
                                onClick={() => setShowDeleteModal(false)}
                                className="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition"
                            >
                                Batal
                            </button>
                            <button
                                onClick={handleDelete}
                                className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                            >
                                Ya, Hapus
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Back button */}
            <div className="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white">
                <Link className="" to="/home">Go back</Link>
            </div>
        </div>
    );
}
