import React from 'react'
import FrequentlyAsked from "@/Components/FrequentlyAsked";
import Layout from "@/Layouts/Layout";
import BannerHero from '@/Components/Hero/BannerHero';

const Faq = () => {
	return (
		<Layout>
			<BannerHero title="Preguntas frecuentes" />
			<div className="py-section container mx-auto p-4">

				<div className="space-y-6">
					<FrequentlyAsked question="¿Cuál es su Horario Laboral?">
						<p>
							Martes a Jueves:
							<br />
							11AM - 8PM
							<br />
							Viernes, Sábado y Domingo:
							<br />
							11AM - 9PM
						</p>
					</FrequentlyAsked>

					<FrequentlyAsked question="¿Dónde están ubicados?">
						<p>
							Estamos ubicados en la Avenida Francisco de Miranda, estacionamiento 2 del Parque Generalísimo Francisco de Miranda (Parque del Este) diagonal al Museo de Transporte, Caracas, Venezuela.
						</p>
					</FrequentlyAsked>

					<FrequentlyAsked question="¿Cuáles son sus métodos de pago?">
						<p>
							Aceptamos pagos en Efectivo, Punto de venta, Pago Móvil o Zelle.
						</p>
					</FrequentlyAsked>

					<FrequentlyAsked question="¿Cuál es el rango de edad para usar el parque?">
						<p>
							Parque Infantil: De 10 Meses a 5 años
							<br />
							Ninja Park: De 6 a 12 años
							<br />
							Otras áreas: De 6 a 65 años
						</p>
					</FrequentlyAsked>

					<FrequentlyAsked question="¿Puedo realizar fiestas o eventos?">
						<p>
							Contamos con 3 planes para que realices tus eventos en nuestro parque. Para más información contáctanos al
							<br />
							<strong>+58 424 1734777</strong>.
						</p>
					</FrequentlyAsked>
				</div>

			</div>
		</Layout>
	)
}

export default Faq