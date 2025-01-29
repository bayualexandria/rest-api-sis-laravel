import React, { useState, useEffect } from "react";
import Main from "../../components/Main/Main";
import repositori from "../../utils/repositories";
import Cookies from "js-cookie";
import Box from "@mui/material/Box";
import Modal from "@mui/material/Modal";
import withReactContent from "sweetalert2-react-content";
import Swal from "sweetalert2";
import DeleteKelasById from "./DeleteKelasById";
import UpdateKelasById from "./UpdateKelasById";
import { Link } from "react-router-dom";

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
    p: 4,
};

const templateModalSuccess = withReactContent(Swal).mixin({
    customClass: {
        confirmButton:
            "bg-sky-500 font-bold text-white outline-none border border-sky-500 rounded-md ml-2 px-2 py-0.5 cursor-pointer",
        cancelButton:
            "bg-rose-500  font-bold text-white outline-none border border-rose-500 rounded-md mr-2 px-2 py-0.5 cursor-pointer",
    },
    buttonsStyling: false,
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

function Kelas() {
    const [kelas, setKelas] = useState([]);
    const [guru, setGuru] = useState([]);
    const [semester, setSemester] = useState([]);
    const [kelasHistory, setKelasHistory] = useState([]);
    const dataToken = Cookies.get("authentication");
    const token = dataToken.split(",");
    const [open, setOpen] = React.useState(false);
    const handleOpen = () => setOpen(true);
    const handleClose = () => setOpen(false);
    const [user, setUser] = useState([]);
    const [datakelasGuru, setDataKelasGuru] = useState([]);

    const getDataKelas = async () => {
        try {
            let response = await fetch(`${repositori}kelas/data-kelas`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: "Bearer " + token[0],
                },
            }).then((res) => res.json());
            setKelas(response.data);
        } catch (e) {}
    };

    const getDataGuru = async () => {
        try {
            let response = await fetch(`${repositori}guru`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: "Bearer " + token[0],
                },
            }).then((res) => res.json());
            setGuru(response.data);
        } catch (error) {}
    };

    const getDataSemester = async () => {
        try {
            let response = await fetch(`${repositori}semester`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: "Bearer " + token[0],
                },
            }).then((res) => res.json());
            setSemester(response.data);
        } catch (error) {}
    };

    // useState data input kelas
    const [kelasID, setKelasID] = useState("");
    const [waliKelas, setWaliKelas] = useState("");
    const [semesterID, setSemesterID] = useState("");
    const [loading, setloading] = useState(false);
    const [loadData, setLoadData] = useState(false);
    const [error, setError] = useState("");

    const dataKelas = {
        kelas_id: kelasID,
        wali_kelas: waliKelas,
        semester_id: semesterID,
    };

    // Add data kelas
    const saveDataKelas = async (e) => {
        setloading(true);
        e.preventDefault();
        try {
            let response = await fetch(`${repositori}kelas`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: "Bearer " + token[0],
                },
                body: JSON.stringify(dataKelas),
            }).then((res) => res.json());
            if (response.status === 403) {
                setTimeout(() => {
                    setloading(false);
                    setError(response.message);
                }, 1000);
            }
            if (response.status === 200) {
                setTimeout(async () => {
                    setloading(false);
                    handleClose();
                    await templateModalSuccess.fire({
                        icon: "success",
                        title: response.message,
                    });
                    setTimeout(() => (window.location.href = "/kelas"), 1000);
                }, 1000);
            }
        } catch (error) {}
    };

    // Get data kelas
    const getKelasHistory = async () => {
        setLoadData(true);
        try {
            let response = await fetch(`${repositori}kelas`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: "Bearer " + token[0],
                },
            }).then((res) => res.json());
            console.log(response);
            if (response.status == 200) {
                setLoadData(false);
                setKelasHistory(response.data);
            }
        } catch (error) {}
    };

    useEffect(() => {
        getDataKelas();
        getDataGuru();
        getDataSemester();
        getKelasHistory();

        const getUserById = async () => {
            try {
                let response = await fetch(`${repositori}user/${token[1]}`, {
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: "Bearer " + token[0],
                    },
                    method: "GET",
                }).then((res) => res.json());
                setUser(response.data);
                console.log("first", response.data);
            } catch (error) {
                return error;
            }
        };
        getUserById();

        const getDataKelasByGuru = async () => {
            try {
                let response = await fetch(
                    `${repositori}kelas/guru/${token[1]}`,
                    {
                        headers: {
                            "Content-Type": "application/json",
                            Authorization: "Bearer " + token[0],
                        },
                        method: "GET",
                    }
                ).then((res) => res.json());
                setDataKelasGuru(response.data);
                console.log("kelas guru", response);
            } catch (error) {
                return error;
            }
        };
        getDataKelasByGuru();
    }, []);

    return (
        <Main>
            <div className="grid grid-cols-6 bg-slate-100">
                <Modal
                    open={open}
                    onClose={handleClose}
                    aria-labelledby="modal-modal-title"
                    aria-describedby="modal-modal-description"
                >
                    <Box sx={style}>
                        <div className="w-full overflow-hidden overflow-y-auto border rounded-lg boder-slate-300 ">
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
                                    className="flex flex-col gap-y-5"
                                    onSubmit={saveDataKelas}
                                >
                                    <div className="flex flex-col gap-y-2">
                                        <label
                                            htmlFor="kelas_id"
                                            className="font-bold text-base text-slate-500"
                                        >
                                            Kelas
                                        </label>
                                        <select
                                            name="kelas_id"
                                            id="kelas_id"
                                            className="border border-sky-500 rounded-md shadow-md outline-none py-1 px-2"
                                            onChange={(e) =>
                                                setKelasID(e.target.value)
                                            }
                                        >
                                            <option value="">
                                                --Pilih Kelas--
                                            </option>
                                            {kelas.map((data) => {
                                                return (
                                                    <option
                                                        value={data.id}
                                                        key={data.id}
                                                    >
                                                        {data.nama_kelas}
                                                        {" | "}
                                                        {data.jurusan}
                                                    </option>
                                                );
                                            })}
                                        </select>
                                        {error.kelas_id ? (
                                            <p className="text-xs font-thin text-rose-500">
                                                {error.kelas_id}
                                            </p>
                                        ) : (
                                            ""
                                        )}
                                    </div>
                                    <div className="flex flex-col gap-y-2">
                                        <label
                                            htmlFor="wali_kelas"
                                            className="font-bold text-base text-slate-500"
                                        >
                                            Wali Kelas
                                        </label>
                                        <select
                                            name="wali_kelas"
                                            id="wali_kelas"
                                            className="border border-sky-500 rounded-md shadow-md outline-none py-1 px-2"
                                            onChange={(e) =>
                                                setWaliKelas(e.target.value)
                                            }
                                        >
                                            <option value="">
                                                --Pilih Wali Kelas--
                                            </option>
                                            {guru.map((data) => {
                                                return (
                                                    <option
                                                        value={data.id}
                                                        key={data.id}
                                                    >
                                                        {data.nama}
                                                    </option>
                                                );
                                            })}
                                        </select>
                                        {error.wali_kelas ? (
                                            <p className="text-xs font-thin text-rose-500">
                                                {error.wali_kelas}
                                            </p>
                                        ) : (
                                            ""
                                        )}
                                    </div>

                                    <div className="flex flex-col gap-y-2">
                                        <label
                                            htmlFor="semester_id"
                                            className="font-bold text-base text-slate-500"
                                        >
                                            Semester
                                        </label>
                                        <select
                                            name="semester_id"
                                            id="semester_id"
                                            className="border border-sky-500 rounded-md shadow-md outline-none py-1 px-2"
                                            onChange={(e) =>
                                                setSemesterID(e.target.value)
                                            }
                                        >
                                            <option value="">
                                                --Pilih Semester--
                                            </option>
                                            {semester.map((data) => {
                                                return (
                                                    <option
                                                        value={data.id}
                                                        key={data.id}
                                                    >
                                                        Semester{" "}
                                                        {data.semester == "II"
                                                            ? "Ganap"
                                                            : "Ganjil"}
                                                        {"-"}
                                                        {data.tahun_pelajaran}
                                                    </option>
                                                );
                                            })}
                                        </select>
                                        {error.semester_id ? (
                                            <p className="text-xs font-thin text-rose-500">
                                                {error.semester_id}
                                            </p>
                                        ) : (
                                            ""
                                        )}
                                    </div>
                                    <div className="flex flex-row justify-end w-full pt-3">
                                        <button
                                            type="submit"
                                            className="rounded-md shadow-md bg-primary text-white border border-primary hover:text-primary hover:bg-white duration-200 transition text-base px-2 py-1"
                                        >
                                            {loading ? (
                                                <div className="gap-x-1 flex flex-row justify-center items-center">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        strokeWidth="1.5"
                                                        stroke="currentColor"
                                                        className="size-5 animate-spin"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                                                        />
                                                    </svg>
                                                    Loading...
                                                </div>
                                            ) : (
                                                "Simpan"
                                            )}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </Box>
                </Modal>
                <div className="col-span-5 col-start-2 p-5 overflow-y-auto">
                    <div className="flex justify-start py-4">
                        <h4 className="font-bold text-xl text-slate-500">
                            Ruang Kelas
                        </h4>
                    </div>
                    {user.status_id == 1 ? (
                        <div className="flex justify-end pb-2">
                            <button
                                className="rounded-md px-2 py-1 shadow-md text-white border border-lime-500 bg-lime-500 hover:bg-white hover:text-lime-500 transition duration-200 flex flex-row gap-x-1 font-bold"
                                onClick={handleOpen}
                            >
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
                                Tambah kelas
                            </button>
                        </div>
                    ) : (
                        ""
                    )}
                    {loadData ? (
                        <div className="w-full justify-center items-center flex flex-col">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                strokeWidth="1.5"
                                stroke="currentColor"
                                className="size-6 animate-spin"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                                />
                            </svg>
                            <p>Loading....</p>
                        </div>
                    ) : (
                        <div className="w-full gap-3  grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 ">
                            {user.status_id == 2
                                ? datakelasGuru.map((k) => {
                                      return (
                                          <div className="w-full rounded-md shadow-md bg-white p-3">
                                              <div className="flex flex-col gap-y-1">
                                                  <Link
                                                      to={`${k.no_induk_guru}/${k.id}`}
                                                      key={k.id}
                                                  >
                                                      <div className="flex flex-row w-full gap-x-1">
                                                          <div className=" w-1/2">
                                                              {k.jurusan ===
                                                              "Teknik Komputer & Jaringan" ? (
                                                                  <svg
                                                                      xmlns="http://www.w3.org/2000/svg"
                                                                      fill="none"
                                                                      viewBox="0 0 24 24"
                                                                      strokeWidth="1.5"
                                                                      stroke="currentColor"
                                                                      className="size-20 text-red-500"
                                                                  >
                                                                      <path
                                                                          strokeLinecap="round"
                                                                          strokeLinejoin="round"
                                                                          d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"
                                                                      />
                                                                  </svg>
                                                              ) : k.jurusan ===
                                                                "Akuntansi" ? (
                                                                  <svg
                                                                      xmlns="http://www.w3.org/2000/svg"
                                                                      fill="none"
                                                                      viewBox="0 0 24 24"
                                                                      strokeWidth="1.5"
                                                                      stroke="currentColor"
                                                                      className="size-20 text-yellow-500"
                                                                  >
                                                                      <path
                                                                          strokeLinecap="round"
                                                                          strokeLinejoin="round"
                                                                          d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z"
                                                                      />
                                                                  </svg>
                                                              ) : k.jurusan ===
                                                                "Pemasaran" ? (
                                                                  <svg
                                                                      xmlns="http://www.w3.org/2000/svg"
                                                                      fill="none"
                                                                      viewBox="0 0 24 24"
                                                                      strokeWidth="1.5"
                                                                      stroke="currentColor"
                                                                      className="size-20 text-green-500"
                                                                  >
                                                                      <path
                                                                          strokeLinecap="round"
                                                                          strokeLinejoin="round"
                                                                          d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"
                                                                      />
                                                                  </svg>
                                                              ) : (
                                                                  <svg
                                                                      xmlns="http://www.w3.org/2000/svg"
                                                                      fill="none"
                                                                      viewBox="0 0 24 24"
                                                                      strokeWidth="1.5"
                                                                      stroke="currentColor"
                                                                      className="size-20 text-blue-500"
                                                                  >
                                                                      <path
                                                                          strokeLinecap="round"
                                                                          strokeLinejoin="round"
                                                                          d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"
                                                                      />
                                                                  </svg>
                                                              )}
                                                          </div>
                                                          <div className="flex flex-col text-left w-1/2 justify-center">
                                                              <h1 className="text-base font-bold text-slate-500">
                                                                  {k.kelas}
                                                              </h1>
                                                              <p className="text-[12px] text-think text-black">
                                                                  {k.jurusan}
                                                              </p>
                                                              <p className="font-bold text-[11px] text-slate-500">
                                                                  {k.semester}/
                                                                  {
                                                                      k.tahun_pelajaran
                                                                  }
                                                              </p>
                                                          </div>
                                                      </div>
                                                  </Link>
                                                  <div className="flex flex-col w-full">
                                                      <p className="text-slate-700 font-bold text-xs">
                                                          Wali Kelas
                                                      </p>
                                                      <div className="flex flex-row items-center justify-between">
                                                          <div className="flex flex-row gap-x-1 items-center ">
                                                              <svg
                                                                  xmlns="http://www.w3.org/2000/svg"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  strokeWidth="1.5"
                                                                  stroke="currentColor"
                                                                  className="size-5 text-sky-500"
                                                              >
                                                                  <path
                                                                      strokeLinecap="round"
                                                                      strokeLinejoin="round"
                                                                      d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                                                  />
                                                              </svg>
                                                              <p className="text-[10px] text-justify">
                                                                  {k.wali_kelas}
                                                              </p>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      );
                                  })
                                : kelasHistory.map((k) => {
                                      return (
                                          <div className="w-full rounded-md shadow-md bg-white p-3">
                                              <div className="flex flex-col gap-y-1">
                                                  <Link
                                                      to={`${k.no_induk_guru}/${k.id}`}
                                                      key={k.id}
                                                  >
                                                      <div className="flex flex-row w-full gap-x-1">
                                                          <div className=" w-1/2">
                                                              {k.jurusan ===
                                                              "Teknik Komputer & Jaringan" ? (
                                                                  <svg
                                                                      xmlns="http://www.w3.org/2000/svg"
                                                                      fill="none"
                                                                      viewBox="0 0 24 24"
                                                                      strokeWidth="1.5"
                                                                      stroke="currentColor"
                                                                      className="size-20 text-red-500"
                                                                  >
                                                                      <path
                                                                          strokeLinecap="round"
                                                                          strokeLinejoin="round"
                                                                          d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"
                                                                      />
                                                                  </svg>
                                                              ) : k.jurusan ===
                                                                "Akuntansi" ? (
                                                                  <svg
                                                                      xmlns="http://www.w3.org/2000/svg"
                                                                      fill="none"
                                                                      viewBox="0 0 24 24"
                                                                      strokeWidth="1.5"
                                                                      stroke="currentColor"
                                                                      className="size-20 text-yellow-500"
                                                                  >
                                                                      <path
                                                                          strokeLinecap="round"
                                                                          strokeLinejoin="round"
                                                                          d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z"
                                                                      />
                                                                  </svg>
                                                              ) : k.jurusan ===
                                                                "Pemasaran" ? (
                                                                  <svg
                                                                      xmlns="http://www.w3.org/2000/svg"
                                                                      fill="none"
                                                                      viewBox="0 0 24 24"
                                                                      strokeWidth="1.5"
                                                                      stroke="currentColor"
                                                                      className="size-20 text-green-500"
                                                                  >
                                                                      <path
                                                                          strokeLinecap="round"
                                                                          strokeLinejoin="round"
                                                                          d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"
                                                                      />
                                                                  </svg>
                                                              ) : (
                                                                  <svg
                                                                      xmlns="http://www.w3.org/2000/svg"
                                                                      fill="none"
                                                                      viewBox="0 0 24 24"
                                                                      strokeWidth="1.5"
                                                                      stroke="currentColor"
                                                                      className="size-20 text-blue-500"
                                                                  >
                                                                      <path
                                                                          strokeLinecap="round"
                                                                          strokeLinejoin="round"
                                                                          d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"
                                                                      />
                                                                  </svg>
                                                              )}
                                                          </div>
                                                          <div className="flex flex-col text-left w-1/2 justify-center">
                                                              <h1 className="text-base font-bold text-slate-500">
                                                                  {k.kelas}
                                                              </h1>
                                                              <p className="text-[12px] text-think text-black">
                                                                  {k.jurusan}
                                                              </p>
                                                              <p className="font-bold text-[11px] text-slate-500">
                                                                  {k.semester}/
                                                                  {
                                                                      k.tahun_pelajaran
                                                                  }
                                                              </p>
                                                          </div>
                                                      </div>
                                                  </Link>
                                                  <div className="flex flex-col w-full">
                                                      <p className="text-slate-700 font-bold text-xs">
                                                          Wali Kelas
                                                      </p>
                                                      <div className="flex flex-row items-center justify-between">
                                                          <div className="flex flex-row gap-x-1 items-center ">
                                                              <svg
                                                                  xmlns="http://www.w3.org/2000/svg"
                                                                  fill="none"
                                                                  viewBox="0 0 24 24"
                                                                  strokeWidth="1.5"
                                                                  stroke="currentColor"
                                                                  className="size-5 text-sky-500"
                                                              >
                                                                  <path
                                                                      strokeLinecap="round"
                                                                      strokeLinejoin="round"
                                                                      d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                                                  />
                                                              </svg>
                                                              <p className="text-[10px] text-justify">
                                                                  {k.wali_kelas}
                                                              </p>
                                                          </div>
                                                          <div className="flex flex-row gap-x-1">
                                                              <UpdateKelasById
                                                                  id={k.id}
                                                              />

                                                              <DeleteKelasById
                                                                  id={k.id}
                                                              />
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      );
                                  })}
                        </div>
                    )}
                </div>
            </div>
        </Main>
    );
}

export default Kelas;
