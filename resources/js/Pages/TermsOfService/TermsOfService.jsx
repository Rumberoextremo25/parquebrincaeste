import BannerHero from '@/Components/Hero/BannerHero'
import Layout from '@/Layouts/Layout'
import { Link } from '@inertiajs/react'
import React from 'react'

const PrivacyPolicy = () => {
	return (
		<Layout title="Normas del Parque Brinca Este">
			<BannerHero title="Normas del Parque Brinca Este" />
			<div className='py-section container'>
				<div className='space-y-6'>
					<div >
						<h5 className='font-medium'>Areas de Caja.</h5>
						<p className='mt-4'>
						1. - Solo se permite 1 adulto por niño sin costo, el representante que quiera entrar al área de PISCINAS, TRAMPOLINES Y NINJA PARK debe cancelar su entrada.
						</p>
						<p className='mt-4'>
						2. En el área de PARQUE INFANTIL solo se permite la entrada a las atracciones de un representante por niño y debe cancelar su entrada y sus medias. El otro acompañante debe esperar en el área de las mesas.
						</p>
						<p className='mt-4'>
						3. Si excede su límite de tiempo debe cancelar el extra.
						</p>
					</div>

					<div>
						<h5 className='font-medium'>ÁREA DE PISCINAS, TRAMPOLINES y NINJA PARK</h5>
						<p className='mt-2'>
							El uso de esta área es BAJO SU PROPIO RIESGO O EL DE SU REPRESENTANTE. Las vueltas y otros trucos pueden ser peligrosos.
							Lea muy bien cada norma:
						</p>
						<p className='mt-4'>
							* CADA ACOMPAÑANTE QUE INGRESE A ESTA ÁREA DEBE HABER CANCELADO SU ENTRADA Y TENER MEDIAS Y BRAZALETE CORRESPONDIENTE.
						</p>
						<p className='mt-4'>
							* El Juego ha sido diseñado para personas entre 6 y 65 años de edad.
						</p>
						<p className='mt-4'>
							* La estatura permitida en esta área del parque es a partir de 1,20 cm.
						</p>
						<p className='mt-4'>
							* El niño o la persona que desee ingresar al área de trampolines, piscinas y ninja park debe cumplir con la edad requerida.
						</p>
						<p className='mt-4'>
							* La distribución del área de los trampolines, ninja park y piscinas es de acuerdo a la edad.
						</p>
						<p className='mt-4'>
							* Siga las reglas del parque y las instrucciones del supervisor del juego o el recreador a cargo.
						</p>
						<p className='mt-4'>
							* El cabello debe estar recogido. SIN EXCEPCIONES.
						</p>
						<p className='mt-4'>
							* No usar el área de juego si nota que está mojado, por favor notificar al personal del parque.
						</p>
						<p className='mt-4'>
							* Los niños deben lanzarse de los toboganes solo en posición sentados y siempre utilizando la dona asignada por el recreador.
						</p>
						<p className='mt-4'>
							* Los usuarios deben esperar su turno para usar las atracciones.
						</p>
						<p className='mt-4'>
							* Los participantes deben entrar con ropa adecuada para los juegos. Ideal pantalones y camisas manga larga para evitar quemaduras por el roce de los juegos.
						</p>
						<p className='mt-4'>
							* Salte siempre con ambos pies y caiga en el centro del trampolín.
						</p>
						<p className='mt-4'>
							* Mantenga siempre el control de su cuerpo.
						</p>
						<p className='mt-4'>
							* Esté atento a su entorno y salte siempre cuando haya personas de su misma edad y talla.
						</p>
						<p className='mt-4'>
							* No se permite saltar si tiene alguna limitación física, deficiencia cardíaca o si se encuentra bajo los efectos de medicamentos como somníferos, alcohol, drogas o si se está embarazada.
						</p>
						<p className='mt-4'>
							* Si está cansado salga del área de saltos y descanse en los lugares habilitados.
						</p>
						<p className='mt-8'>
							<strong>
							RECUERDE el peso máximo para utilizar ésta área del parque es de 150 kg y la edad máxima son 65 años de edad.
							</strong>
						</p>
					</div>

					<div>
						<h5 className='font-medium'>PARQUE INFANTIL.</h5>
						<p className='mt-4'>
							1. El PARQUE INFANTIL está diseñado para bebés de 10 meses hasta niños y niñas de 5 años de edad.
						</p>
						<p className='mt-4'>
							2. Siga las reglas del parque y las instrucciones del supervisor del juego o el recreador a cargo.
						</p>
						<p className='mt-4'>
							3. No usar el área de juego si nota que está mojado, por favor notificar al personal del parque.
						</p>
						<p className='mt-4'>
							4. Los niños deben lanzarse de los toboganes solo en posición sentados.
						</p>
						<p className='mt-4'>
							5. Los niños deben entrar con ropa adecuada para los juegos. Ideal pantalones y camisas manga larga para evitar quemaduras por el roce de los juegos.
						</p>
						<p className='mt-5'>
							6. Por favor notificar a nuestro personal de BRINCAESTE identificados con los uniformes de colores, cualquier problema o preocupación generada dentro de nuestras instalaciones.
						</p>
						<p className='mt-6'>
							7. Los representantes que estén dentro del Baby Park no pueden hacer uso de los trampolines de esa área.
						</p>
						<p className='mt-6'>
							8. Los representantes pueden entrar a la plataforma como acompañante, seran exonerados con la entrada pero deberan pagar las medias.
						</p>
					</div>

					<div>
						<h5 className='font-medium'>PROHIBIDO.</h5>
						<p className='mt-4'>
							8. El cabello debe estar recogido. SIN EXCEPCIONES.
						</p>
						<p className='mt-4'>
							9. Las comidas, bebidas, gomas de mascar, caramelos, etc. Deben ser consumidas en el área de espera.
						</p>
					</div>

					<div>
						<h5 className='font-medium'>ESTÁ PROHIBIDO.</h5>
						<p className='mt-4'>
							1. Ingresar sin brazalete a las atracciones.
						</p>
						<p className='mt-4'>
							2. Hacer uso de las atracciones sin medias antideslizantes BRINCAESTE.
						</p>
						<p className='mt-4'>
							3. Ingresar al área de piscina, trampolines y ninja park con reloj, celulares, cadenas, correas, juguetes y accesorios de bisutería. No se permiten objetos punzantes o no autorizados (cámaras de fotos, llaveros, etc.)
						</p>
						<p className='mt-4'>
							4. No se permite entrar con lentes de ningún tipo a la plataforma.
						</p>
						<p className='mt-4'>
							5. Entrar con comidas, bebidas, gomas de mascar, caramelos o cualquier tipo de alimentos a las atracciones.
						</p>
						<p className='mt-4'>
							6. Ingresar con prendas de vestir cortas como: shorts, faldas, vestidos.
						</p>
						<p className='mt-4'>
							7. Retirar del área del PARQUE INFANTIL, TRAMPOLINES, PISCINAS Y NINJA PARK cualquier implemento de juego.
						</p>
						<p className='mt-4'>
							8. Ingresar con prendas de vestir cortas como: short, faldas y vestidos.
						</p>
						<p className='mt-4'>
							9. Hacer uso de las atracciones con el cabello suelto.
						</p>
						<p className='mt-4'>
							10. Lanzarse de cabeza en los toboganes, trampolines ni piscinas.
						</p>
						<p className='mt-4'>
							11. El rebote doble o afectar de algún modo el rebote de algún otro saltador.
						</p>
						<p className='mt-4'>
							12. Intentar cualquier pirueta o salto que exceda su nivel de habilidad.
						</p>
					</div>

				</div>
			</div>
		</Layout>
	)
}

export default PrivacyPolicy
