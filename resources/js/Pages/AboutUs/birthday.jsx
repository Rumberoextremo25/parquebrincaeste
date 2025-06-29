import React from "react";
import { CheckCircleIcon } from "@heroicons/react/24/outline"; // Se mantiene, asumiendo que @heroicons/react está instalado

// Componente Layout Simplificado (incrustado para evitar errores de importación)
const Layout = ({ children, title }) => {
    return (
        <div className="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
            <header className="bg-white shadow py-4">
                <div className="container mx-auto px-4">
                    <h1 className="text-2xl font-bold text-gray-800">
                        {title || "Mi Aplicación"}
                    </h1>
                </div>
            </header>
            <main>{children}</main>
            <footer className="bg-gray-800 text-white py-8 mt-12">
                <div className="container mx-auto text-center">
                    <p>
                        &copy; {new Date().getFullYear()} Brinca Este. Todos los
                        derechos reservados.
                    </p>
                </div>
            </footer>
        </div>
    );
};

// Componente BannerHero Simplificado (incrustado para evitar errores de importación)
const BannerHero = ({ title, desc, img }) => {
    const backgroundStyle = img
        ? { backgroundImage: `url(${img})` }
        : { backgroundColor: "#FFDDC1" }; // Color de fallback si no hay imagen

    return (
        <div
            className="relative bg-cover bg-center h-80 flex items-center justify-center text-white text-center px-4"
            style={backgroundStyle}
        >
            <div className="absolute inset-0 bg-black opacity-50"></div>{" "}
            {/* Overlay oscuro */}
            <div className="relative z-10 p-4 rounded-lg">
                <h1 className="text-4xl sm:text-5xl font-extrabold mb-4 drop-shadow-lg leading-tight">
                    {title}
                </h1>
                {desc && (
                    <p className="text-lg sm:text-xl max-w-2xl mx-auto drop-shadow-md">
                        {desc}
                    </p>
                )}
            </div>
        </div>
    );
};

// Componente TitleSection Simplificado (incrustado para evitar errores de importación)
const TitleSection = ({ title, subTitle, description, className }) => {
    return (
        <div className={`mb-8 ${className}`}>
            {subTitle && (
                <p className="text-sm font-semibold uppercase text-blue-600 mb-2">
                    {subTitle}
                </p>
            )}
            <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                {title}
            </h2>
            {description && (
                <p className="text-md text-gray-600 max-w-2xl mx-auto">
                    {description}
                </p>
            )}
        </div>
    );
};

const birthday = () => {
    const packagesData = [
        {
            title: "Plan Básico",
            price: "$300 USD",
            features: [
                "1 Cilindro decorativo",
                "1 Paraban personalizado con el motivo de la fiesta (puede escoger entre luna o círculo).",
                "150 globos y una alfombra o grama de base.",
                "Cumpleañero GRATIS.",
                "10 comidas incluidas de cada concesión del parque.",
            ],
            imgSrc: "/img/about/plan basico.PNG",
            imgAlt: "Plan Básico de Fiesta",
            isImageRight: false,
        },
        {
            title: "Plan Medio",
            price: "$550 USD",
            features: [
                "3 cilindros (1 con motivo y 2 de colores).",
                "1 paraban personalizado con el motivo de la fiesta (que se escoge entre luna o vinil).",
                "200 globos.",
                "Una alfombra o grama de base.",
                "15 brazaletes más 15 medias antideslizantes. Válido para niños.",
                "15 comidas de cada concesión del parque.",
            ],
            imgSrc: "/img/about/plan medio.PNG",
            imgAlt: "Plan Medio de Fiesta",
            isImageRight: true,
        },
        {
            title: "Plan VIP",
            price: "$800 USD",
            features: [
                "3 cilindros + PISO ROTULADO.",
                "1 paraban personalizado + nombre del cumpleañero de 3 metros cuadrado.",
                "400 globos (normales y cromados).",
                "1 letra, figura o número tamaño 85cm de altura.",
                "25 brazaletes más 25 medias antideslizantes.",
                "25 comidas incluidas de cada concesión del parque.",
            ],
            imgSrc: "/img/about/Plan VIP.PNG",
            imgAlt: "Plan VIP de Fiesta",
            isImageRight: false,
        },
    ];

    return (
        <div className="container mx-auto p-4 py-16 sm:py-24">
            <TitleSection
                className="text-center mb-16"
                title="Nuestros Paquetes de Fiestas Inolvidables"
                subTitle="LA MEJOR OPCIÓN PARA CELEBRAR"
                description="Elige el paquete perfecto que se adapte a tus necesidades y presupuesto. Cada opción está pensada para ofrecer la máxima diversión y comodidad."
            />

            <div className="space-y-20">
                {packagesData.map((plan, index) => (
                    <section
                        key={index}
                        className={`bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100
                                       flex flex-col lg:flex-row items-center gap-10 p-8 sm:p-12
                                       transform transition-all duration-500 hover:scale-[1.01] hover:shadow-3xl
                                       ${
                                           plan.isImageRight
                                               ? "lg:flex-row-reverse"
                                               : ""
                                       }`}
                    >
                        <div className="flex-1 w-full lg:w-1/2">
                            <img
                                src={plan.imgSrc}
                                alt={plan.imgAlt}
                                className="w-full h-80 sm:h-96 object-cover rounded-2xl shadow-lg transition-transform duration-300 hover:scale-105"
                                loading="lazy"
                                onError={(e) => {
                                    e.target.onerror = null;
                                    e.target.src =
                                        "https://placehold.co/600x400/FFC0CB/808080?text=Imagen+del+Plan";
                                }}
                            />
                        </div>

                        <div className="flex-1 w-full lg:w-1/2 text-center lg:text-left">
                            <h3 className="text-5xl font-extrabold text-blue-600 mb-4 leading-tight drop-shadow-md">
                                {plan.title}
                            </h3>
                            <p className="text-4xl font-bold text-purple-700 mb-6 drop-shadow-sm">
                                {plan.price}
                            </p>
                            <ul className="space-y-3 text-lg text-gray-700 leading-relaxed text-left max-w-md mx-auto lg:mx-0">
                                {plan.features.map((feature, i) => (
                                    <li key={i} className="flex items-center">
                                        <CheckCircleIcon className="w-6 h-6 text-green-500 mr-3 flex-shrink-0" />
                                        <span>{feature}</span>
                                    </li>
                                ))}
                            </ul>
                            <div className="mt-10 flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                                <a
                                    href="mailto:brincaeste@gmail.com" // Reemplaza con tu correo electrónico
                                    className="inline-flex items-center justify-center px-6 py-3 bg-blue-500 text-white font-bold text-lg rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-400 focus:ring-opacity-75"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        strokeWidth="2"
                                        stroke="currentColor"
                                        className="w-6 h-6 mr-2"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"
                                        />
                                    </svg>
                                    Contacto por Email
                                </a>
                                <a
                                    href="https://wa.me/584123508826?text=Hola!%20Me%20interesan%20los%20paquetes%20de%20fiestas%20de%20Brinca%20Este." // Reemplaza con tu número de WhatsApp
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="inline-flex items-center justify-center px-6 py-3 bg-green-500 text-white font-bold text-lg rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-green-400 focus:ring-opacity-75"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        className="w-6 h-6 mr-2"
                                    >
                                        <path d="M12.001 2.001C6.48 2.001 2 6.481 2 12.001c0 2.457.854 4.717 2.292 6.482L2.01 22.001l4.01-1.989A9.956 9.956 0 0012.001 22.001c5.52 0 10-4.48 10-10s-4.48-10-10-10zm.001 1.5a8.502 8.502 0 017.34 4.316l.006.012.083.153a.85.85 0 01-.137 1.096c-.22.204-.56.23-.816.096L17 11.233c-.156-.084-.336-.082-.493-.016l-3.326 1.706c-.456.233-1.002.046-1.22-.383-.024-.047-.04-.09-.052-.132l-.248-.82c-.08-.258-.292-.47-.549-.55L8.5 10.5c-.38-.112-.76-.046-1.07.172-.03.024-.058.05-.084.075L5.7 12.553c-.31.3-.43.72-.34 1.12.06.27.2.5.38.68.17.16.38.25.6.28.2.02.4.004.59-.044l.007-.002a.85.85 0 01.442-.093c.12-.008.24-.002.358.02l.142.04c.48.134.98.02 1.25-.26.04-.04.07-.08.096-.123l.005-.01c.2-.38-.07-.85-.45-1.05-.03-.02-.05-.03-.08-.05l-1.2-.6c-.2-.1-.4-.1-.6-.05-.18.06-.34.16-.46.3-.12.14-.19.3-.2.47-.02.2-.004.4.048.59l.002.007.82.248c.258.08.47.292.55.549l.55 1.76c.112.38.25.74.45 1.07.2.32.48.58.8.76.32.18.68.3.96.38.28.08.56.09.84.06l.042-.006c.07-.008.14-.01.21-.01.24 0 .47.05.68.14l.007.004c.4.1.84-.04 1.15-.35l1.85-2.1c.3-.34.42-.78.33-1.18-.06-.27-.2-.5-.38-.68-.17-.16-.38-.25-.6-.28-.2-.02-.4-.004-.59.044L17 14.767c-.156.084-.336.082-.493-.016l-3.326-1.706c-.456-.233-1.002-.046-1.22.383-.024.047-.04.09-.052.132l-.248.82c-.08.258-.292.47-.549.55L8.5 10.5c-.38-.112-.76-.046-1.07.172-.03.024-.058.05-.084.075L5.7 12.553c-.31.3-.43.72-.34 1.12.06.27.2.5.38.68.17.16.38.25.6.28.2.02.4.004.59-.044l.007-.002a.85.85 0 01.442-.093c.12-.008.24-.002.358.02l.142.04c.48.134.98.02 1.25-.26.04-.04.07-.08.096-.123l.005-.01c.2-.38-.07-.85-.45-1.05-.03-.02-.05-.03-.08-.05z" />
                                    </svg>
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                    </section>
                ))}
            </div>
        </div>
    );
};

export default birthday;
