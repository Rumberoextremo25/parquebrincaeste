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
					{[
						{
							title: "Áreas de Caja",
							content: [
								"1. Solo se permite 1 adulto por niño sin costo. El representante que quiera entrar al área de PISCINAS, TRAMPOLINES Y NINJA PARK debe cancelar su entrada.",
								"2. En el área de PARQUE INFANTIL solo se permite la entrada a las atracciones de un representante por niño, quien debe cancelar su entrada y sus medias. El otro acompañante debe esperar en el área de las mesas.",
								"3. Si excede su límite de tiempo, debe cancelar el extra."
							]
						},
						{
							title: "ÁREA DE PISCINAS, TRAMPOLINES y NINJA PARK",
							content: [
								"El uso de esta área es BAJO SU PROPIO RIESGO O EL DE SU REPRESENTANTE. Las vueltas y otros trucos pueden ser peligrosos. Lea muy bien cada norma:",
								"* CADA ACOMPAÑANTE QUE INGRESE A ESTA ÁREA DEBE HABER CANCELADO SU ENTRADA Y TENER MEDIAS Y BRAZALETE CORRESPONDIENTE.",
								"* El juego ha sido diseñado para personas entre 6 y 65 años de edad.",
								"* La estatura permitida en esta área del parque es a partir de 1,20 cm.",
								"* El niño o la persona que desee ingresar al área de trampolines, piscinas y ninja park debe cumplir con la edad requerida.",
								"* La distribución del área de los trampolines, ninja park y piscinas es de acuerdo a la edad.",
								"* Siga las reglas del parque y las instrucciones del supervisor del juego o el recreador a cargo.",
								"* El cabello debe estar recogido. SIN EXCEPCIONES.",
								"* No usar el área de juego si nota que está mojado; por favor, notifique al personal del parque.",
								"* Los niños deben lanzarse de los toboganes solo en posición sentados y siempre utilizando la dona asignada por el recreador.",
								"* Los usuarios deben esperar su turno para usar las atracciones.",
								"* Los participantes deben entrar con ropa adecuada para los juegos. Ideal pantalones y camisas de manga larga para evitar quemaduras por el roce de los juegos.",
								"* Salte siempre con ambos pies y caiga en el centro del trampolín.",
								"* Mantenga siempre el control de su cuerpo.",
								"* Esté atento a su entorno y salte siempre cuando haya personas de su misma edad y talla.",
								"* No se permite saltar si tiene alguna limitación física, deficiencia cardíaca o si se encuentra bajo los efectos de medicamentos como somníferos, alcohol, drogas o si se está embarazada.",
								"* Si está cansado, salga del área de saltos y descanse en los lugares habilitados.",
								<strong>RECUERDE el peso máximo para utilizar esta área del parque es de 150 kg y la edad máxima son 65 años de edad.</strong>
							]
						},
						{
							title: "PARQUE INFANTIL",
							content: [
								"1. El PARQUE INFANTIL está diseñado para bebés de 10 meses hasta niños y niñas de 5 años de edad.",
								"2. Siga las reglas del parque y las instrucciones del supervisor del juego o el recreador a cargo.",
								"3. No usar el área de juego si nota que está mojado; por favor, notifique al personal del parque.",
								"4. Los niños deben lanzarse de los toboganes solo en posición sentados.",
								"5. Los niños deben entrar con ropa adecuada para los juegos. Ideal pantalones y camisas de manga larga para evitar quemaduras por el roce de los juegos.",
								"6. Por favor, notifique a nuestro personal de BRINCAESTE, identificados con los uniformes de colores, cualquier problema o preocupación generada dentro de nuestras instalaciones.",
								"7. Los representantes que estén dentro del Baby Park no pueden hacer uso de los trampolines de esa área.",
								"8. Los representantes pueden entrar a la plataforma como acompañante, serán exonerados con la entrada, pero deberán pagar las medias."
							]
						},
						{
							title: "PROHIBIDO",
							content: [
								"1. El cabello debe estar recogido. SIN EXCEPCIONES.",
								"2. Las comidas, bebidas, gomas de mascar, caramelos, etc., deben ser consumidas en el área de espera."
							]
						},
						{
							title: "ESTÁ PROHIBIDO",
							content: [
								"1. Ingresar sin brazalete a las atracciones.",
								"2. Hacer uso de las atracciones sin medias antideslizantes BRINCAESTE.",
								"3. Ingresar al área de piscina, trampolines y ninja park con reloj, celulares, cadenas, correas, juguetes y accesorios de bisutería. No se permiten objetos punzantes o no autorizados (cámaras de fotos, llaveros, etc.).",
								"4. No se permite entrar con lentes de ningún tipo a la plataforma.",
								"5. Entrar con comidas, bebidas, gomas de mascar, caramelos o cualquier tipo de alimentos a las atracciones.",
								"6. Ingresar con prendas de vestir cortas como: shorts, faldas, vestidos.",
								"7. Retirar del área del PARQUE INFANTIL, TRAMPOLINES, PISCINAS Y NINJA PARK cualquier implemento de juego.",
								"8. Hacer uso de las atracciones con el cabello suelto.",
								"9. Lanzarse de cabeza en los toboganes, trampolines ni piscinas.",
								"10. El rebote doble o afectar de algún modo el rebote de algún otro saltador.",
								"11. Intentar cualquier pirueta o salto que exceda su nivel de habilidad."
							]
						}
					].map((section, index) => (
						<div key={index}>
							<h5 className='font-medium'>{section.title}</h5>
							{section.content.map((text, i) => (
								<p className='mt-4' key={i}>{text}</p>
							))}
						</div>
					))}
				</div>
			</div>
		</Layout>
	)
}

export default PrivacyPolicy
