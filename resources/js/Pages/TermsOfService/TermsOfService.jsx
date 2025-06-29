import React from 'react';
// Restaurando el uso de aliases (@/) para componentes compartidos.
// Asegúrate de que estos aliases estén configurados correctamente en tu configuración de Vite/JS.
import BannerHero from '@/Components/Hero/BannerHero'; 
import Layout from '@/Layouts/Layout'; 
import { Link } from '@inertiajs/react'; // Se mantiene si se usa en el futuro, aunque no directamente en esta versión.

const PrivacyPolicy = () => { // Nombre del componente PrivacyPolicy
    const rulesData = [ // Contenido que simula "Reglas del Parque" o "Términos y Condiciones"
        {
            title: "1. Información que Recopilamos",
            content: [
                "Recopilamos información personal que usted nos proporciona directamente al usar nuestros servicios, como su nombre, dirección de correo electrónico, número de teléfono y datos de pago al realizar una compra.",
                "También podemos recopilar información automáticamente a través de cookies y tecnologías de seguimiento, como su dirección IP, tipo de navegador, páginas visitadas y el tiempo que pasa en nuestro sitio web. Esto nos ayuda a mejorar su experiencia."
            ]
        },
        {
            title: "2. Uso de su Información",
            content: [
                "Utilizamos la información recopilada para procesar sus pedidos, proporcionarle los servicios solicitados y mejorar su experiencia en nuestro parque.",
                "Podemos usar su información para comunicarnos con usted sobre promociones, eventos y actualizaciones relevantes, siempre que haya dado su consentimiento para recibir dichas comunicaciones.",
                "La información también se utiliza para fines internos, como análisis de datos, investigación y desarrollo de nuevos servicios, y para garantizar la seguridad de nuestras operaciones."
            ]
        },
        {
            title: "3. Compartir y Divulgar Información",
            content: [
                "Brinca Este Jumping Park se compromete a no vender, alquilar ni divulgar su información personal a terceros, excepto en los casos necesarios para el cumplimiento de nuestros servicios (por ejemplo, procesadores de pago).",
                "Podemos divulgar información si es requerido por ley o si creemos de buena fe que dicha acción es necesaria para cumplir con un proceso legal, proteger nuestros derechos o la seguridad de nuestros usuarios o del público."
            ]
        },
        {
            title: "4. Seguridad de los Datos",
            content: [
                "Implementamos medidas de seguridad técnicas, administrativas y físicas para proteger su información personal contra accesos no autorizados, uso indebido, pérdida o destrucción. Sin embargo, ninguna transmisión de datos por Internet es 100% segura y no podemos garantizar la seguridad absoluta.",
                "Usted es responsable de mantener la confidencialidad de cualquier contraseña o credencial de acceso que se le proporcione para nuestros servicios."
            ]
        },
        {
            title: "5. Sus Derechos",
            content: [
                "Usted tiene derecho a acceder, corregir, actualizar o eliminar su información personal en cualquier momento. Si desea ejercer estos derechos, contáctenos a través de los canales proporcionados.",
                "También puede optar por no recibir comunicaciones de marketing de nuestra parte siguiendo las instrucciones de cancelación de suscripción en nuestros correos electrónicos."
            ]
        },
        {
            title: "6. Cambios en esta Política de Privacidad",
            content: [
                "Nos reservamos el derecho de modificar esta Política de Privacidad en cualquier momento. Las actualizaciones se publicarán en esta página con una nueva fecha de 'Última Actualización'. Le recomendamos revisar periódicamente esta política para estar informado sobre cómo protegemos su información.",
                "El uso continuado de nuestros servicios después de cualquier modificación de esta Política de Privacidad implica su aceptación de dichos cambios."
            ]
        },
        {
            title: "7. Contáctenos",
            content: [
                "Si tiene preguntas o inquietudes sobre esta Política de Privacidad o nuestras prácticas de datos, no dude en contactarnos en: ",
                <a key="email-link" href="mailto:brincaeste@gmail.com" className="text-blue-600 hover:text-blue-800 underline transition-colors duration-200">brincaeste@gmail.com</a>,
                "o a través de nuestra sección de contacto en el sitio web."
            ]
        }
    ];

    return (
        <Layout title="Términos y Condiciones"> {/* Cambiado el título del layout para reflejar mejor el contenido de "reglas/términos" */}
            {/* Sección de Banner Hero */}
            <BannerHero
                title="TÉRMINOS Y CONDICIONES"
                desc="Para una experiencia óptima y segura en Brinca Este Jumping Park, le invitamos a leer nuestros términos y condiciones. Su cumplimiento asegura la diversión de todos."
                //img="/img/home/IMG_9783.jpg" // Puedes usar una imagen más representativa o un color sólido en el BannerHero
            />

            <div className="container mx-auto p-4 py-16 sm:py-24"> {/* Contenedor principal con padding responsivo */}
                {/* Contenedor principal del documento de normas */}
                <div className="bg-white p-8 sm:p-12 lg:p-16 rounded-2xl shadow-xl border border-gray-100 max-w-4xl mx-auto">
                    <h1 className="text-4xl font-extrabold text-gray-900 mb-8 text-center leading-tight">
                        Acuerdo de Uso y Normativa del Parque
                    </h1>
                    <p className="text-gray-600 text-lg mb-10 text-center">
                        Este documento establece las normas y directrices que rigen el uso de las instalaciones y servicios de Brinca Este Jumping Park. Al ingresar a nuestras instalaciones, usted acepta estos términos.
                    </p>

                    <div className="space-y-10"> {/* Espacio entre secciones de normas */}
                        {rulesData.map((section, index) => (
                            <div key={index}>
                                <h2 className="font-semibold text-2xl text-gray-800 mb-3 pb-2 border-b border-gray-200">
                                    {section.title}
                                </h2>
                                <ul className="mt-4 space-y-3 list-inside">
                                    {section.content.map((text, i) => (
                                        <li key={i} className="text-base text-gray-700 leading-relaxed flex items-start">
                                            {typeof text === 'string' && text.startsWith('http') ? ( // Manejar enlaces que no son solo texto
                                                <span className="text-blue-500 mr-2 mt-1">&#8226;</span>
                                            ) : typeof text === 'object' && text.type === 'a' ? ( // Manejar el componente <a> si se pasa directamente
                                                <span className="text-blue-500 mr-2 mt-1">&#8226;</span>
                                            ) : (
                                                <span className="text-blue-500 mr-2 mt-1">&#8226;</span>
                                            )}
                                            {text}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>

                    {/* Pie de página de la política */}
                    <p className="text-center text-gray-500 text-sm mt-16 pt-8 border-t border-gray-200">
                        Última actualización: 28 de Junio de 2025
                    </p>
                </div>
            </div>
        </Layout>
    );
}

export default PrivacyPolicy; // Exportación del componente como PrivacyPolicy
