import React from "react";
import FrequentlyAsked from "@/Components/FrequentlyAsked";
import Layout from "@/Layouts/Layout";
import BannerHero from "@/Components/Hero/BannerHero";

const Promotion = () => {
    return (
        <Layout>
            <BannerHero title="PROMOCIONES" />
            <div className="py-section container">
                <div className="overflow-x-auto">
                    <table className="min-w-full border-collapse shadow-lg rounded-lg overflow-hidden">
                        <thead className="bg-pink-600 text-white">
                            <tr>
                                <th className="p-4 text-left">Promoción</th>
                                <th className="p-4 text-left">Descripción</th>
                                <th className="p-4 text-left">Contacto</th>
                                <th className="p-4 text-left">Costo</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white">
                            {/* Paquete Promo Accesorios */}
                            <tr className="border-b hover:bg-gray-100 transition duration-300">
                                <td className="p-4 flex flex-col items-center">
                                    <img
                                        src="/img/promotion/gift.png"
                                        alt="gift"
                                        className="w-16 h-16 object-cover mb-2"
                                    />
                                    <span className="font-semibold">Promo Accesorios</span>
                                </td>
                                <td className="p-4">
                                    Descubre los sabores de Brinca Manía, donde cada bocado es una aventura.
                                </td>
                                <td className="p-4">
                                    <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                    <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                    <strong>Correo:</strong> tickets@parquebrincaeste.com
                                </td>
                                <td className="p-4 text-green-600 font-bold">$20.00</td>
                            </tr>

                            {/* Paquete Promo Brincaclaus */}
                            <tr className="border-b hover:bg-gray-100 transition duration-300">
                                <td className="p-4 flex flex-col items-center">
                                    <img
                                        src="/img/promotion/gift.png"
                                        alt="gift"
                                        className="w-16 h-16 object-cover mb-2"
                                    />
                                    <span className="font-semibold">Promo BrincaClaus</span>
                                </td>
                                <td className="p-4">PROXIMAMENTE !!.</td>
                                <td className="p-4">
                                    <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                    <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                    <strong>Correo:</strong> tickets@parquebrincaeste.com
                                </td>
                                <td className="p-4 text-green-600 font-bold">$25.00</td>
                            </tr>

                            {/* Paquete Promo Duo */}
                            <tr className="border-b hover:bg-gray-100 transition duration-300">
                                <td className="p-4 flex flex-col items-center">
                                    <img
                                        src="/img/promotion/gift.png"
                                        alt="gift"
                                        className="w-16 h-16 object-cover mb-2"
                                    />
                                    <span className="font-semibold">Promo Duo</span>
                                </td>
                                <td className="p-4">
                                    Deléitate con nuestros irresistibles pancakes, perfectos para cualquier momento del día.
                                </td>
                                <td className="p-4">
                                    <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                    <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                    <strong>Correo:</strong> tickets@parquebrincaeste.com
                                </td>
                                <td className="p-4 text-green-600 font-bold">$15.00</td>
                            </tr>

                            {/* Paquete Promo Escolar */}
                            <tr className="border-b hover:bg-gray-100 transition duration-300">
                                <td className="p-4 flex flex-col items-center">
                                    <img
                                        src="/img/promotion/gift.png"
                                        alt="gift"
                                        className="w-16 h-16 object-cover mb-2"
                                    />
                                    <span className="font-semibold">Promo Escolar</span>
                                </td>
                                <td className="p-4">
                                    Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.
                                </td>
                                <td className="p-4">
                                    <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                    <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                    <strong>Correo:</strong> tickets@parquebrincaeste.com
                                </td>
                                <td className="p-4 text-green-600 font-bold">$30.00</td>
                            </tr>

                            {/* Paquete Promo Familiar */}
                            <tr className="border-b hover:bg-gray-100 transition duration-300">
                                <td className="p-4 flex flex-col items-center">
                                    <img
                                        src="/img/promotion/gift.png"
                                        alt="gift"
                                        className="w-16 h-16 object-cover mb-2"
                                    />
                                    <span className="font-semibold">Promo Familiar</span>
                                </td>
                                <td className="p-4">
                                    Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.
                                </td>
                                <td className="p-4">
                                    <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                    <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                    <strong>Correo:</strong> tickets@parquebrincaeste.com
                                </td>
                                <td className="p-4 text-green-600 font-bold">$35.00</td>
                            </tr>

                            {/* Paquete Promo Kinder */}
                            <tr className="border-b hover:bg-gray-100 transition duration-300">
                                <td className="p-4 flex flex-col items-center">
                                    <img
                                        src="/img/promotion/gift.png"
                                        alt="gift"
                                        className="w-16 h-16 object-cover mb-2"
                                    />
                                    <span className="font-semibold">Promo Kinder</span>
                                </td>
                                <td className="p-4">
                                    Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.
                                </td>
                                <td className="p-4">
                                    <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                    <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                    <strong>Correo:</strong> tickets@parquebrincaeste.com
                                </td>
                                <td className="p-4 text-green-600 font-bold">$28.00</td>
                            </tr>

                            {/* Paquete Promo 2x1 */}
                            <tr className="border-b hover:bg-gray-100 transition duration-300">
                                <td className="p-4 flex flex-col items-center">
                                    <img
                                        src="/img/promotion/gift.png"
                                        alt="gift"
                                        className="w-16 h-16 object-cover mb-2"
                                    />
                                    <span className="font-semibold">Promo 2x1</span>
                                </td>
                                <td className="p-4">
                                    Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.
                                </td>
                                <td className="p-4">
                                    <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                    <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                    <strong>Correo:</strong> tickets@parquebrincaeste.com
                                </td>
                                <td className="p-4 text-green-600 font-bold">$18.00</td>
                            </tr>

                            {/* Paquete Promo Racha */}
                            <tr className="border-b hover:bg-gray-100 transition duration-300">
                                <td className="p-4 flex flex-col items-center">
                                    <img
                                        src="/img/promotion/gift.png"
                                        alt="gift"
                                        className="w-16 h-16 object-cover mb-2"
                                    />
                                    <span className="font-semibold">Promo Racha</span>
                                </td>
                                <td className="p-4">
                                    Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.
                                </td>
                                <td className="p-4">
                                    <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                    <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                    <strong>Correo:</strong> tickets@parquebrincaeste.com
                                </td>
                                <td className="p-4 text-green-600 font-bold">$22.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </Layout>
    );
};

export default Promotion;