import React, { useState } from "react";
import Box from "@mui/material/Box";
import Modal from "@mui/material/Modal";
import Cookies from "js-cookie";
import repositori from "../../../utils/repositories";

const style = {
    position: "absolute",
    top: "50%",
    left: "50%",
    transform: "translate(-50%, -50%)",
    width: 400,
    bgcolor: "background.paper",
    border: "2px solid #FFF",
    boxShadow: 24,
    borderRadius: 1,
    p: 3,
};

function AddDataSiswa() {
    const [open, setOpen] = React.useState(false);
    const handleOpen = () => setOpen(true);
    const handleClose = () => setOpen(false);
    const [nip, setNIP] = useState("");
    const [nama, setNama] = useState("");
    const [email, setEmail] = useState("");
    const [noHP, setNoHP] = useState("");
    const [jenisKelamin, setJenisKelamin] = useState("");

    const [alamat, setAlamat] = useState("");
    const [error, setError] = useState("");
    const data = {
        nip,
        nama,
        email,
        no_hp: noHP,
        jenis_kelamin: jenisKelamin,
        alamat,
    };

    const saveGuru = async (e) => {
        e.preventDefault();
        const dataToken = Cookies.get("authentication");

        const token = dataToken.split(",");

        try {
            let response = await fetch(`${repositori}guru`, {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                    "Content-Type": "application/json",
                    Authorization: "Bearer " + token[0],
                },
            }).then((res) => res.json());
            console.log(response.status);
            if (response.status === 403) {
                setError(response.message);
                console.log(response.message.nip);
            }
            console.log(response);
        } catch (error) {
            console.log(error.message);
        }
    };

    return (
        <>
            <div className="cursor-pointer outline-none" onClick={handleOpen}>
                <div className="rounded-md p-1 flex flex-row justify-center items-center font-bold border border-lime-500 text-sm gap-x-1 hover:bg-white hover:text-lime-500 cursor-pointer bg-lime-500 text-white transition duration-200">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        strokeWidth="1.5"
                        stroke="currentColor"
                        className="size-6"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            d="M12 4.5v15m7.5-7.5h-15"
                        />
                    </svg>

                    <p>Tambah data</p>
                </div>
            </div>
            <Modal
                open={open}
                onClose={handleClose}
                aria-labelledby="modal-modal-title"
                aria-describedby="modal-modal-description"
            >
                <Box sx={style}>
                    <div className="w-full overflow-hidden overflow-y-auto border rounded-lg boder-slate-300 ">
                        <h1 className="font-bold text-xl p-3 text-slate-700">
                            Add Data Guru
                        </h1>
                        <hr className="text-slate-500" />
                        <div className="absolute top-1 right-1">
                            <button
                                className="outline-none w-5 h-5 border border-slate-300 flex justify-center items-center rounded-full text-slate-500 transition duration-200 hover:border-slate-500 hover:bg-white"
                                onClick={handleClose}
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    strokeWidth="1.5"
                                    stroke="currentColor"
                                    className="size-3 "
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="M6 18 18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div className="flex flex-col w-full p-3">
                            <form
                                onSubmit={saveGuru}
                                className="flex flex-col w-full gap-y-3 overflow-auto"
                            >
                                <div className="flex flex-col w-full gap-y-1">
                                    <label
                                        className="font-bold text-sm text-slate-500"
                                        htmlFor="nip"
                                    >
                                        NIP
                                    </label>
                                    <input
                                        onChange={(e) => setNIP(e.target.value)}
                                        id="nip"
                                        name="nip"
                                        type="text"
                                        className="outline-none border border-primary rounded-md shadow-md w-full py-1 px-2"
                                    />
                                    {error.nip ? (
                                        <p className="text-xs text-rose-500 font-thin">
                                            {error.nip}
                                        </p>
                                    ) : (
                                        ""
                                    )}
                                </div>
                                <div className="flex flex-col w-full gap-y-1">
                                    <label
                                        className="font-bold text-sm text-slate-500"
                                        htmlFor="nama"
                                    >
                                        Nama Lengkap
                                    </label>
                                    <input
                                        onChange={(e) =>
                                            setNama(e.target.value)
                                        }
                                        id="nama"
                                        name="nama"
                                        type="text"
                                        className="outline-none border border-primary rounded-md shadow-md w-full py-1 px-2"
                                    />
                                    {error.nama ? (
                                        <p className="text-xs text-rose-500 font-thin">
                                            {error.nama}
                                        </p>
                                    ) : (
                                        ""
                                    )}
                                </div>

                                <div className="flex flex-col w-full gap-y-1">
                                    <label
                                        className="font-bold text-sm text-slate-500"
                                        htmlFor="email"
                                    >
                                        Email
                                    </label>
                                    <input
                                        onChange={(e) =>
                                            setEmail(e.target.value)
                                        }
                                        id="email"
                                        name="email"
                                        type="text"
                                        className="outline-none border border-primary rounded-md shadow-md w-full py-1 px-2"
                                    />
                                    {error.email ? (
                                        <p className="text-xs text-rose-500 font-thin">
                                            {error.email}
                                        </p>
                                    ) : (
                                        ""
                                    )}
                                </div>
                                <div className="flex flex-col gap-y-1">
                                    <label
                                        htmlFor="jenis_kelamin"
                                        className="font-bold text-sm text-slate-500"
                                    >
                                        Jenis Kelamin
                                    </label>
                                    <div className="flex flex-row gap-x-5">
                                        <div className="flex flex-row gap-x-3">
                                            <input
                                                type="radio"
                                                name="jenis_kelamin"
                                                onChange={(e) =>
                                                    setJenisKelamin(
                                                        e.target.value
                                                    )
                                                }
                                                value="Laki-laki"
                                                className="border border-sky-500"
                                                id="Laki-laki"
                                            />
                                            <label htmlFor="Laki-laki">
                                                Laki-laki
                                            </label>
                                        </div>
                                        <div className="flex flex-row gap-x-3">
                                            <input
                                                type="radio"
                                                name="jenis_kelamin"
                                                className="border border-sky-500"
                                                onChange={(e) =>
                                                    setJenisKelamin(
                                                        e.target.value
                                                    )
                                                }
                                                value="Perempuan"
                                                id="Perempuan"
                                            />
                                            <label htmlFor="Perempuan">
                                                Perempuan
                                            </label>
                                        </div>
                                    </div>
                                    {error.jenis_kelamin ? (
                                        <p className="text-xs text-rose-500 font-thin">
                                            {error.jenis_kelamin}
                                        </p>
                                    ) : (
                                        ""
                                    )}
                                </div>
                                <div className="flex flex-col w-full gap-y-1">
                                    <label
                                        className="font-bold text-sm text-slate-500"
                                        htmlFor="no_hp"
                                    >
                                        No. Handphone
                                    </label>
                                    <input
                                        onChange={(e) =>
                                            setNoHP(e.target.value)
                                        }
                                        id="no_hp"
                                        name="no_hp"
                                        type="text"
                                        className="outline-none border border-primary rounded-md shadow-md w-full py-1 px-2"
                                    />
                                    {error.no_hp ? (
                                        <p className="text-xs text-rose-500 font-thin">
                                            {error.no_hp}
                                        </p>
                                    ) : (
                                        ""
                                    )}
                                </div>

                                <div className="flex flex-col gap-y-1">
                                    <label
                                        htmlFor="alamat"
                                        className="font-bold text-sm text-slate-500"
                                    >
                                        Alamat
                                    </label>
                                    <textarea
                                        onChange={(e) =>
                                            setAlamat(e.target.value)
                                        }
                                        name="alamat"
                                        id="alamat"
                                        className="rounded-md outline-none border border-primary h-32 p-5"
                                    ></textarea>
                                    {error.alamat ? (
                                        <p className="text-xs text-rose-500 font-thin">
                                            {error.alamat}
                                        </p>
                                    ) : (
                                        ""
                                    )}
                                </div>
                                <div className="flex flex-row justify-end gap-x-2">
                                    <button
                                        onClick={handleClose}
                                        className="rounded-full outline-none border border-slate-300 shadow-md py-1 px-6 text-sm font-bold text-black bg-slate-300 hover:bg-slate-500 hover:text-white transition duration-200"
                                    >
                                        Keluar
                                    </button>
                                    <button
                                        type="submit"
                                        className="rounded-full outline-none border border-primary shadow-md py-1 px-6 text-sm font-bold text-white bg-primary hover:bg-white hover:text-primary transition duration-200"
                                    >
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </Box>
            </Modal>
        </>
    );
}

export default AddDataSiswa;
