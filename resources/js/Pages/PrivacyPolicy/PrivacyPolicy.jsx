import BannerHero from '@/Components/Hero/BannerHero'
import Layout from '@/Layouts/Layout'
import React from 'react'

const PrivacyPolicy = () => {
	return (
		<Layout title="Política de privacidad">
			<BannerHero title="Políticas de privacidad" />
			<div className='py-section container'>
				<div className='space-y-6'>
					<div>
						<h5 className='font-medium text-lg'>Recolección y uso de la información.</h5>
						<p className='mt-2 text-base'>
							La política de Privacidad y Seguridad de Brinca Este Jumping Park aplica a la recopilación y uso de la información que usted nos suministre a través de nuestro Sitio Web. Dicha información será utilizada únicamente por Brinca Este Jumping Park en el cumplimiento de sus fines.
						</p>
						<p className='text-base'>
							Brinca Este Jumping Park utiliza sistemas automatizados para la detección y prevención de ataques informáticos, minimizando la posibilidad de sufrir daños o alteraciones en la información disponible en el Sitio Web. Los mismos nos permiten generar reportes y bitácoras de los accesos indebidos desde y hacia nuestro sitio.
						</p>
						<p className='text-base'>
							La información se utilizará con el propósito para la que fue solicitada. Brinca Este Jumping Park respeta su derecho a la privacidad, y no proveerá a terceras personas la información personal de sus usuarios sin su consentimiento, a no ser que sea requerido por las leyes vigentes.
						</p>
					</div>

					<div>
						<h5 className='font-medium text-lg'>Cookies.</h5>
						<p className='mt-2 text-base'>
							Utilizamos tecnología de rastreo con “cookies”. El Usuario puede utilizar la configuración de su navegador para deshabilitar el uso de “cookies”. Si son deshabilitadas podrá seguir navegando en nuestro Sitio Web, pero con algunas restricciones.
						</p>
					</div>

					<div>
						<h5 className='font-medium text-lg'>Seguridad de la información</h5>
						<p className='mt-2 text-base'>
							Este sitio web cuenta con medidas de seguridad para proteger la información personal proporcionada por sus visitantes contra accesos no autorizados, revelación, destrucción o mal uso.
						</p>
					</div>

					<div>
						<h5 className='font-medium text-lg'>Actualización de datos</h5>
						<p className='mt-2 text-base'>
							Periódicamente realizamos revisión y actualización de los datos que usted nos proporciona a través del Sitio Web que puedan ser modificados.
						</p>
					</div>

					<div>
						<h5 className='font-medium text-lg'>Actualización de políticas</h5>
						<p className='mt-2 text-base'>
							La política de privacidad y seguridad de Brinca Este Jumping Park fue revisada en el mes de enero del año en curso. Podremos modificarla periódicamente; en dicho caso, comunicaremos la política modificada en nuestro Sitio Web.
						</p>
					</div>

					<div>
						<h5 className='font-medium text-lg'>Contáctenos</h5>
						<p className='mt-2 text-base'>
							Brinca Este Jumping Park está comprometido en proteger los datos que usted nos proporcione. Si tiene alguna pregunta, observación o inquietud sobre nuestra Política de Privacidad y Seguridad o nuestros Términos y Condiciones, póngase en contacto con nosotros enviándonos un mensaje de correo electrónico a <a href="mailto:brincaeste@gmail.com" className="text-blue-600 underline">brincaeste@gmail.com</a>.
						</p>
					</div>
				</div>
			</div>
		</Layout>
	)
}

export default PrivacyPolicy

