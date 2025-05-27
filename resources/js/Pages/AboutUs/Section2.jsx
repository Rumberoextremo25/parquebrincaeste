import React from "react";
import { Link } from "@inertiajs/react";
import TitleSection from "@/Components/TitleSection";

const Section2 = () => {
	return (
		<div className=" lg:bg-transparent lg:bg-[url('/img/about/BANNER-ABOUT-US.webp')] lg:bg-[length:85%]  lg:bg-left  lg:bg-no-repeat">
			<div className="py-section container ">
				<div className=" grid lg:grid-cols-12">
					<div className="rounded lg:col-span-8 lg:col-start-5 lg:bg-gray-50   lg:p-8">
						<TitleSection title="MISION" subTitle="" />

						<div className="mt-2">
							<p className="text">
								Ofrecer un espacio de recreación en el que nuestro público disfrute de
								excelentes atracciones, donde la diversión y la alegría sean los
								protagonistas.
							</p>
							<p className="mt-2">
								BRINCAESTE, promete ser un lugar diferente e innovador para compartir en familia y amigos, 
								donde cada uno de nuestros ambientes se transformen en una experiencia memorable y perfecta para repetir.
							</p>
						</div>
						<div className="mt-4">
						<TitleSection title="VISION" subTitle="" />
							<p className="text">
							Convertirnos en el líder en entretenimiento familiar de
							Venezuela.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
};

export default Section2;

