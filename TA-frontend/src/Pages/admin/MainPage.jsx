import { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { getToken } from "../../utils/utils";
import { ThreeDot } from "react-loading-indicators";

export default function MainPage() {
    const [event, setEvent] = useState([]);
    const [loading, setLoading] = useState(true);
    const [name, setName] = useState("");
    const [description, setDescription] = useState("");
    const [date, setDate] = useState("");
    const [showModal, setShowModal] = useState(false);
    const [deleteModal, setDeleteModal] = useState(false);
    const [selectedId, setSelectedId] = useState(null);

    const navigate = useNavigate();

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
        setLoading(false);
    }

    async function handleAspirationEvent(e) {
        e.preventDefault();
        const res = await fetch("http://localhost:8000/api/events", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": `Bearer ${getToken()}`
            },
            body: JSON.stringify({
                name,
                description,
                date
            })
        });
        const data = await res.json();

        if (res.status === 200) {
            setShowModal(false);
            setName("");
            setDescription("");
            setDate("");
            fetchEvent(); // refresh list
        }

        console.log(data.event)
    }

    async function confirmDelete() {
        await fetch(`http://localhost:8000/api/events/${selectedId}`, {
            method: "DELETE",
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${getToken()}`,
                "Content-Type": "application/json"
            }
        });
        setDeleteModal(false);
        setSelectedId(null);
        fetchEvent();
    }

    async function handleLogout(e) {
        e.preventDefault();
        const res = await fetch("http://localhost:8000/api/logout", {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${getToken()}`,
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
        });
        if (res.status === 200) {
            navigate("/");
        }
    }

    useEffect(() => {
        fetchEvent();
    }, []);

    return (
        <div className="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-6">
            <div className="max-w-5xl mx-auto pb-5">
                {/* Header */}
                <div className="text-center mb-10">
                    <h1 className="text-3xl font-bold text-red-700">Admin Dashboard</h1>
                    <p className="mt-3 text-gray-600">
                        Kelola aspirasi dan event sekolah dengan mudah.
                    </p>
                </div>

                {/* Shortcut */}
                <div className="flex justify-center mb-8 flex-wrap gap-4">
                    <Link
                        to="/home/aspirations"
                        className="px-5 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition"
                    >
                        📜 Lihat Aspirasi Harian
                    </Link>
                    <button
                        onClick={() => setShowModal(true)}
                        className="py-2 px-5 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition"
                    >
                        + Tambahkan Event
                    </button>
                </div>

                {/* Event List */}
                <div>
                    <h2 className="text-xl font-semibold text-gray-800 mb-6">
                        📅 Daftar Event
                    </h2>
                    {loading ? (
                        <div className="flex justify-center items-center min-h-[50vh]">
                            <ThreeDot color="#ff4747" size="medium" />
                        </div>
                    ) : event.length > 0 ? (
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            {event.map((item) => (
                                <div
                                    key={item.id}
                                    className="p-5 bg-white rounded-xl shadow-md border-l-8 border-red-400 hover:shadow-lg transition"
                                >
                                    <h3 className="text-lg font-bold text-red-700 mb-2">
                                        {item.name}
                                    </h3>
                                    <p className="text-gray-700 mb-4">
                                        {item.description || "Tidak ada deskripsi"}
                                    </p>
                                    <div className="flex justify-between">
                                        <Link
                                            to={`/home/aspirations/events/${item.id}`}
                                            className="inline-block px-4 py-1 bg-red-200 text-gray-700 rounded-lg hover:bg-red-300 transition"
                                        >
                                            Lihat Aspirasi →
                                        </Link>
                                        <button
                                            onClick={() => {
                                                setSelectedId(item.id);
                                                setDeleteModal(true);
                                            }}
                                            className="ml-4 text-gray-500 px-3 py-1 rounded-lg text-sm hover:bg-gray-200 cursor-pointer"
                                        >
                                            🗑
                                        </button>

                                    </div>

                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-center text-gray-500">
                            🙁 Belum ada event yang tersedia.
                        </p>
                    )}

                    {/* Modal Tambah Event */}
                    {showModal && (
                        <div className="fixed inset-0 p-5 bg-opacity-75 flex justify-center items-center z-50 backdrop-blur-sm">
                            <div className="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
                                <h2 className="text-xl font-bold text-red-600 mb-4">
                                    Tambah Event Baru
                                </h2>
                                <form onSubmit={handleAspirationEvent} className="space-y-4">
                                    <input
                                        type="text"
                                        placeholder="Nama Event"
                                        onChange={(e) => setName(e.target.value)}
                                        className="w-full border px-3 py-2 rounded-lg"
                                        required
                                    />
                                    <textarea
                                        placeholder="Deskripsi"
                                        onChange={(e) => setDescription(e.target.value)}
                                        className="w-full border px-3 py-2 rounded-lg"
                                    ></textarea>
                                    <input
                                        type="date"
                                        onChange={(e) => setDate(e.target.value)}
                                        className="w-full border px-3 py-2 rounded-lg"
                                        required
                                    />
                                    <div className="flex justify-end gap-3">
                                        <button
                                            type="button"
                                            onClick={() => setShowModal(false)}
                                            className="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            type="submit"
                                            className="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
                                        >
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    )}

                    {/* Modal Konfirmasi Delete */}
                    {deleteModal && (
                        <div className="fixed inset-0 bg-opacity-75 flex justify-center items-center z-50 backdrop-blur-sm">
                            <div className="bg-white rounded-xl p-6 w-full max-w-sm shadow-lg">
                                <h2 className="text-lg font-bold text-red-600 mb-4">Konfirmasi</h2>
                                <p className="text-gray-700 mb-6">
                                    Apakah Anda yakin ingin menghapus event ini?
                                </p>
                                <div className="flex justify-end gap-3">
                                    <button
                                        onClick={() => setDeleteModal(false)}
                                        className="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        onClick={confirmDelete}
                                        className="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Logout Button */}
                    <div className="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white cursor-pointer hover:bg-red-600">
                        <button onClick={handleLogout}>Logout</button>
                    </div>
                </div>
            </div>
        </div>
    );
}
