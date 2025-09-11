import { useEffect, useState } from "react";
import { getToken } from "../../utils/utils";

export default function AspirationEvent() {
    const [message, setMessage] = useState("");
    const [to, setTo] = useState("");
    const [eventId, setEventId] = useState("");
    const [other_to, setOtherTo] = useState("");
    const [showModal, setShowModal] = useState(false);
    const [events, setEvent] = useState([]);
    const [hint, setHint] = useState(false);

    const divisions = [
        "Divisi Keamanan",
        "Divisi Kebersihan",
        "Divisi Acara",
        "Divisi Dokumentasi",
        "Divisi Humas",
        "Divisi Perlengkapan",
        "lainnya"
    ];

    const badWords = ["anjing", "bangsat", "goblok", "tai", "kontol", "bego", "anj", "anjay", "anjir", "a n j i n g", "tolol", "monyet", "babi", "memek", "pepek", "puki", "jancuk", "jancok", "kampret", "kntl", "kntol", "pantek", "panteq", "bajingan", "fuck", "shit", "asshole"];
    const containBadWords = badWords.some(words =>
        message.toLowerCase().includes(words)
    );

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
    }

    async function handleAspiration(e) {
        e.preventDefault()
        const res = await fetch("http://localhost:8000/api/aspiration/events", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify({
                "message": message,
                "to": to,
                "event_id": eventId,
                "other_to": other_to
            })
        })
        const data = await res.json()

        if (res.status == 200) {
            setShowModal(true)
        }

        console.log(data.aspiration_event)
    }

    useEffect(() => {
        fetchEvent()
    }, [])

    return (
        <div className="flex items-center justify-center min-h-screen bg-gradient-to-br from-red-50 via-white to-red-50 p-4">
            <div className="card border bg-white shadow-2xl rounded-3xl p-8 w-full max-w-md  border-red-500 relative overflow-hidden">

                <div className="absolute -top-20 -right-20 w-40 h-40 rounded-full bg-red-100 opacity-30"></div>
                <div className="absolute -bottom-16 -left-16 w-32 h-32 rounded-full bg-red-100 opacity-30"></div>

                <div className="relative z-10">
                    <div className="flex justify-center mb-4">
                        <img className="w-25 h-25" src="/logo-mpk.jpg" alt="logo mpk" />
                    </div>

                    <h1 className="text-3xl font-bold text-gray-800 text-center mb-3">
                        <span className="text-red-600">Ten</span>Aspiration
                    </h1>
                    <p className="text-gray-600 text-center mb-6 text-sm px-4">
                        Sampaikan aspirasimu secara <span className="font-semibold text-red-500">anonim</span> kepada MPK, OSIS, atau Sekolah.
                    </p>

                    <form onSubmit={handleAspiration} className="space-y-5">
                        {/* Pesan */}
                        <div>
                            <label className=" text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                Pesan Aspirasi
                            </label>
                            <textarea
                                onChange={(e) => setMessage(e.target.value)}
                                placeholder="Tulis aspirasi kamu di sini..."
                                rows="3"
                                className="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition"
                            />
                        </div>

                        {/* Event */}
                        <div>
                            <label className=" text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Event
                            </label>
                            <select
                                onChange={(e) => setEventId(e.target.value)}
                                className="w-full border border-gray-300 rounded-lg p-3 bg-white focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition appearance-none">
                                <option value="">-- Pilih Event --</option>
                                {
                                    events.map((item, i) => (
                                        <option key={i} value={item.id}>{item.name}</option>
                                    ))
                                }
                            </select>
                        </div>

                        {/* Tujuan */}
                        <div>
                            <label className=" text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Tujuan (ke)
                            </label>
                            <select
                                onChange={(e) => setTo(e.target.value)}
                                className="w-full border border-gray-300 rounded-lg p-3 bg-white focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition appearance-none">
                                <option value="">-- Pilih Tujuan --</option>
                                {
                                    divisions.map((div, i) => (
                                        <option key={i} value={div}>{div}</option>
                                    ))
                                }
                            </select>
                            {to === "lainnya" && (
                                <input
                                    type="text"
                                    placeholder="Tuliskan tujuan (ke) lain..."
                                    value={other_to}
                                    onChange={(e) => setOtherTo(e.target.value)}
                                    className="mt-2 w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition"
                                />
                            )}
                        </div>

                        {/* Button Kirim */}
                        <button
                            type="submit"
                            disabled={!(message && to && eventId) || containBadWords}
                            className={`w-full py-3 rounded-lg font-semibold shadow-md transition-all duration-200 ${message && to && eventId && !containBadWords
                                ? "bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white"
                                : "bg-gray-200 text-white cursor-not-allowed"
                                }`}>
                            <div className="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Kirim Aspirasi
                            </div>
                        </button>

                        {/* Panduan */}
                        <button
                            type="button"
                            onClick={() => setHint(true)}
                            className=" w-full py-3 rounded-lg font-semibold shadow-md transition-all duration-200 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white"
                        >
                            Panduan
                        </button>
                    </form>

                    {/* Modal sukses */}
                    {showModal && (
                        <div className="fixed inset-0 bg-opacity-75 flex items-center justify-center z-50 backdrop-blur-sm">
                            <div className="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-red-500 animate-pop">
                                <div className="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                    <svg className="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h2 className="text-xl font-bold text-gray-800 mb-2">Aspirasi Terkirim!</h2>
                                <p className="text-gray-600 mb-4">Terima kasih telah menyampaikan aspirasimu.</p>
                                <button
                                    onClick={() => window.location.reload()}
                                    className="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2">
                                    Mengerti
                                </button>
                            </div>
                        </div>
                    )}

                    {/* Modal panduan */}
                    {hint && (
                        <div className="fixed inset-0 bg-opacity-75 flex items-center justify-center z-50 backdrop-blur-sm">
                            <div className="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-blue-500 animate-pop">
                                <h2 className="text-xl font-bold text-gray-800 mb-2">Panduan</h2>
                                <p className="text-gray-600 mb-4 text-sm">
                                    1. Pilih tujuan aspirasi (MPK, OSIS, atau SEKOLAH).<br />
                                    2. Tulis pesan aspirasi kalian di kolom "Pesan aspirasi".<br />
                                    3. Menggunakan bahasa yang baik dan benar.<br />
                                    4. Klik tombol "Kirim Aspirasi".
                                </p>
                                <button
                                    onClick={() => setHint(false)}
                                    className="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                                >
                                    Mengerti
                                </button>
                            </div>
                        </div>
                    )}

                    <p className="mt-6 text-xs text-gray-500 text-center italic">
                        "Suara Anda penting bagi kami. Setiap aspirasi akan ditinjau oleh MPK."
                    </p>
                </div>
            </div>
        </div>
    );
}
