import React from 'react'
import FrequentlyAsked from "@/Components/FrequentlyAsked";
import Layout from "@/Layouts/Layout";
import BannerHero from '@/Components/Hero/BannerHero';

const Package = () => {
    return (
        <Layout>
            <BannerHero title="EVENTOS PRIVADOS & CUMPLEAÑOS" />
            <div className="py-section container">
                <table className="min-w-full border-collapse shadow-lg rounded-lg overflow-hidden">
                    <thead className="bg-pink-600 text-white">
                        <tr>
                            <th className="p-4">Paquete</th>
                            <th className="p-4">Descripción</th>
                            <th className="p-4">Costo</th>
                            <th className="p-4">Disponibilidad</th>
                            <th className="p-4">Contacto</th>
                        </tr>
                    </thead>
                    <tbody className="bg-white">
                        {/* Paquete PLAN BASICO */}
                        <tr className="hover:bg-gray-100 transition duration-300">
                            <td className="p-4">
                                <img
                                    src="/img/paquetes/planbasico.png"
                                    alt=""
                                    className="w-64 h-64 object-cover"
                                />
                            </td>
                            <td className="p-4">
                                3 Horas de Disfrute.
                                <br />
                                ¡ El Cumpleañero Tiene Entrada, Medias y Comidas Gratis !
                                <br />
                                * 1 Cilindro
                                <br />
                                * 1 Paraban Personalizado con el motivo de la fiesta
                                <br />
                                * 150 Globos y una alfombra o grama de base.
                                <br />
                                * 11 brazaletes + 11 medias antideslizantes (Para los niños).
                                <br />
                                * 11 Comidas de cada concesion del parque.
                            </td>
                            <td className="p-4"><strong>$600 <br /> $750</strong></td>
                            <td className="p-4 text-green-500">Martes a Jueves<br /> Viernes a Domingo</td>
                            <td className="p-4">
                                <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                <strong>Correo:</strong> tickets@parquebrincaeste.com
                            </td>
                        </tr>

                        {/* Paquete MEDIUM */}
                        <tr className="hover:bg-gray-100 transition duration-300">
                            <td className="p-4">
                                <img
                                    src="/img/paquetes/IMG_7017.png"
                                    alt=""
                                    className="w-64 h-64 object-cover"
                                />
                            </td>
                            <td className="p-4">
                                3 Horas de Disfrute.
                                <br />
                                ¡ El Cumpleañero Tiene Entrada, Medias y Comidas Gratis !
                                <br />
                                * 3 Cilindro
                                <br />
                                * 1 Paraban Personalizado con el motivo de la fiesta
                                <br />
                                * 200 Globos y una alfombra o grama de base.
                                <br />
                                * 16 brazaletes + 16 medias antideslizantes (Para los niños).
                                <br />
                                * 16 Comidas de cada concesion del parque.
                            </td>
                            <td className="p-4"><strong>$800 <br /> $980</strong></td>
                            <td className="p-4 text-green-500">Martes a Jueves<br /> Viernes a Domingo</td>
                            <td className="p-4">
                                <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                <strong>Correo:</strong> tickets@parquebrincaeste.com
                            </td>
                        </tr>

                        {/* Paquete VIP */}
                        <tr className="hover:bg-gray-100 transition duration-300">
                            <td className="p-4">
                                <img
                                    src="/img/paquetes/IMG_7019.png"
                                    alt=""
                                    className="w-64 h-64 object-cover"
                                />
                            </td>
                            <td className="p-4">
                                3 Horas de Disfrute.
                                <br />
                                ¡ El Cumpleañero Tiene Entrada, Medias y Comidas Gratis !
                                <br />
                                * 3 Cilindro y Piso Rotulado
                                <br />
                                * 1 Paraban Personalizado + Nombre del Cumpleañero (3m2)
                                <br />
                                * 400 Globos (Normales y Cromados).
                                <br />
                                * 1 Letra, Figura o Numero de 85cm.
                                <br />
                                * 26 brazaletes + 26 medias antideslizantes (Para los niños).
                                <br />
                                * 26 comidas de cada concesion del parque.
                            </td>
                            <td className="p-4"><strong>$1200<br /> $1500</strong></td>
                            <td className="p-4 text-green-500">Martes a Jueves<br /> Viernes a Domingo</td>
                            <td className="p-4">
                                <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                <strong>Correo:</strong> tickets@parquebrincaeste.com
                            </td>
                        </tr>

                        {/* Paquete Sopla Tus Velitas */}
                        <tr className="hover:bg-gray-100 transition duration-300">
                            <td className="p-4">
                                <img
                                    src="/img/paquetes/IMG_7145.png"
                                    alt=""
                                    className="w-64 h-64 object-cover"
                                />
                            </td>
                            <td className="p-4">
                                2 Horas de Disfrute.
                                <br />
                                ¡ El Cumpleañero Entra Gratis !
                                <br />
                                * Incluye entradas y medias para 10 amiguitos.
                                <br />
                                * Trae tu torta te obsequiamos:
                                <br />
                                * 2 Pizzas Familiares
                                <br />
                                * 2 Refrescos
                            </td>
                            <td className="p-4"><strong>$200<br /> $250</strong></td>
                            <td className="p-4 text-green-500">Martes a Jueves<br /> Viernes a Domingo</td>
                            <td className="p-4">
                                <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                <strong>Correo:</strong> tickets@parquebrincaeste.com
                            </td>
                        </tr>
                        {/* Paquete Gratis */}
                        <tr className="hover:bg-gray-100 transition duration-300">
                            <td className="p-4">
                                <img
                                    src="/img/paquetes/IMG_4416.png"
                                    alt=""
                                    className="w-64 h-64 object-cover"
                                />
                            </td>
                            <td className="p-4">
                                Trae a 5 personas que adquieran entradas y medias.
                                <br />
                                ¡ El Cumpleañero tiene entrada y medias gratis !
                                <br />
                                * traer tu torta y canta cumple con nosotros.
                            </td>
                            <td className="p-4"><strong>Gratis<br /></strong></td>
                            <td className="p-4 text-green-500">Martes a Domingos<br /></td>
                            <td className="p-4">
                                <strong>Nombre:</strong> Brinca Este 2024 C.A<br />
                                <strong>Teléfono:</strong> (+58) 412-3508826<br />
                                <strong>Correo:</strong> tickets@parquebrincaeste.com
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Layout>
    )
}

export default Package