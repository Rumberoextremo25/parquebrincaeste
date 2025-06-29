import React from 'react';
// Rutas relativas ajustadas asumiendo que PrivacyPolicy.jsx está en resources/js/Pages/PrivacyPolicy/
// y BannerHero está en resources/js/Components/Hero/.
// Se sube dos niveles (../../) desde PrivacyPolicy/ para llegar a resources/js/,
// luego se baja a Components/Hero/.
import BannerHero from '../../Components/Hero/BannerHero'; 
// Ruta relativa ajustada asumiendo que Layout está en resources/js/Layouts/.
// Se sube dos niveles (../../) desde PrivacyPolicy/ para llegar a resources/js/,
// luego se baja a Layouts/.
import Layout from '../../Layouts/Layout'; 
// No se usa Card, ListDescription, TitleSection directamente en este rediseño,
// por lo que los he omitido en esta versión para mayor claridad, a menos que se usen internamente en los componentes importados.

const PrivacyPolicy = () => {
    return (
        <Layout title="Política de Privacidad">
            {/* Sección de Banner Hero */}
            <BannerHero
                title="NUESTRA POLÍTICA DE PRIVACIDAD"
                desc="Entiende cómo Brinca Este Jumping Park protege tu información y respeta tu privacidad. Tu seguridad es nuestra prioridad."
                //img="/img/home/IMG_9783.jpg" // Puedes usar una imagen más neutra o un color sólido en el BannerHero
            />

            <div className="container mx-auto p-4 py-16 sm:py-24"> {/* Contenedor principal con padding responsivo */}
                {/* Contenedor principal del documento de políticas */}
                <div className="bg-white p-8 sm:p-12 lg:p-16 rounded-2xl shadow-xl border border-gray-100 max-w-4xl mx-auto">
                    <h1 className="text-4xl font-extrabold text-gray-900 mb-8 text-center leading-tight">
                        Declaración de Privacidad
                    </h1>
                    <p className="text-gray-600 text-lg mb-10 text-center">
                        Brinca Este Jumping Park se compromete a proteger la privacidad de sus usuarios. Esta política describe cómo recopilamos, usamos y protegemos su información personal.
                    </p>

                    <div className="space-y-10"> {/* Espacio entre secciones de política */}
                        {/* Sección: Recolección y uso de la información */}
                        <div>
                            <h2 className="font-semibold text-2xl text-gray-800 mb-3 pb-2 border-b border-gray-200">
                                1. Recolección y uso de la información
                            </h2>
                            <p className="mt-4 text-base text-gray-700 leading-relaxed">
                                La política de Privacidad y Seguridad de Brinca Este Jumping Park aplica a la recopilación y uso de la información que usted nos suministre a través de nuestro Sitio Web. Dicha información será utilizada únicamente por Brinca Este Jumping Park en el cumplimiento de sus fines.
                            </p>
                            <p className="mt-4 text-base text-gray-700 leading-relaxed">
                                Brinca Este Jumping Park utiliza sistemas automatizados para la detección y prevención de ataques informáticos, minimizando la posibilidad de sufrir daños o alteraciones en la información disponible en el Sitio Web. Los mismos nos permiten generar reportes y bitácoras de los accesos indebidos desde y hacia nuestro sitio.
                            </p>
                            <p className="mt-4 text-base text-gray-700 leading-relaxed">
                                La información se utilizará con el propósito para la que fue solicitada. Brinca Este Jumping Park respeta su derecho a la privacidad, y no proveerá a terceras personas la información personal de sus usuarios sin su consentimiento, a no ser que sea requerido por las leyes vigentes.
                            </p>
                        </div>

                        {/* Sección: Cookies. */}
                        <div>
                            <h2 className="font-semibold text-2xl text-gray-800 mb-3 pb-2 border-b border-gray-200">
                                2. Cookies
                            </h2>
                            <p className="mt-4 text-base text-gray-700 leading-relaxed">
                                Utilizamos tecnología de rastreo con "cookies". Las cookies son pequeños archivos de texto que se almacenan en su dispositivo para mejorar su experiencia de navegación, recordar sus preferencias y analizar el uso de nuestro sitio. El Usuario puede utilizar la configuración de su navegador para deshabilitar el uso de "cookies". Si son deshabilitadas, podrá seguir navegando en nuestro Sitio Web, pero con algunas restricciones en la funcionalidad y personalización.
                            </p>
                        </div>

                        {/* Sección: Seguridad de la información */}
                        <div>
                            <h2 className="font-semibold text-2xl text-gray-800 mb-3 pb-2 border-b border-gray-200">
                                3. Seguridad de la información
                            </h2>
                            <p className="mt-4 text-base text-gray-700 leading-relaxed">
                                Este sitio web cuenta con robustas medidas de seguridad técnicas, administrativas y físicas para proteger la información personal proporcionada por sus visitantes contra accesos no autorizados, revelación, alteración, destrucción o mal uso. Nos esforzamos continuamente en implementar las mejores prácticas de seguridad para salvaguardar sus datos.
                            </p>
                        </div>

                        {/* Sección: Actualización de datos */}
                        <div>
                            <h2 className="font-semibold text-2xl text-gray-800 mb-3 pb-2 border-b border-gray-200">
                                4. Actualización de datos
                            </h2>
                            <p className="mt-4 text-base text-gray-700 leading-relaxed">
                                Periódicamente realizamos revisión y actualización de los datos que usted nos proporciona a través del Sitio Web. Le animamos a mantener su información personal actualizada para asegurar la precisión de nuestros registros y la calidad de nuestros servicios.
                            </p>
                        </div>

                        {/* Sección: Actualización de políticas */}
                        <div>
                            <h2 className="font-semibold text-2xl text-gray-800 mb-3 pb-2 border-b border-gray-200">
                                5. Actualización de políticas
                            </h2>
                            <p className="mt-4 text-base text-gray-700 leading-relaxed">
                                La política de privacidad y seguridad de Brinca Este Jumping Park fue revisada por última vez en el mes de enero del año en curso. Podremos modificarla periódicamente para reflejar cambios en nuestras prácticas o requisitos legales. En dicho caso, comunicaremos la política modificada en nuestro Sitio Web y actualizaremos la fecha de "Última Actualización" en la parte inferior de esta página. Le recomendamos revisar esta política regularmente.
                            </p>
                        </div>

                        {/* Sección: Contáctenos */}
                        <div>
                            <h2 className="font-semibold text-2xl text-gray-800 mb-3 pb-2 border-b border-gray-200">
                                6. Contáctenos
                            </h2>
                            <p className="mt-4 text-base text-gray-700 leading-relaxed">
                                Brinca Este Jumping Park está comprometido en proteger los datos que usted nos proporcione. Si tiene alguna pregunta, observación o inquietud sobre nuestra Política de Privacidad y Seguridad o nuestros Términos y Condiciones, por favor no dude en ponerse en contacto con nosotros enviándonos un mensaje de correo electrónico a <a href="mailto:brincaeste@gmail.com" className="text-blue-600 hover:text-blue-800 underline transition-colors duration-200">brincaeste@gmail.com</a>. Estaremos encantados de atenderle.
                            </p>
                        </div>
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

export default PrivacyPolicy;

