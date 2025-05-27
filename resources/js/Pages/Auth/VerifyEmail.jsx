import React from 'react';
import Button from '@/Components/Button';
import Guest from '@/Layouts/Guest';
import { Head, Link, useForm } from '@inertiajs/react';

export default function VerifyEmail({ status }) {
	const { post, processing } = useForm();

	const submit = (e) => {
		e.preventDefault();

		post(route('verification.send'));
	};

	return (
		<Guest>
			<Head title="Email Verification" />

			<div className="mb-4 text-sm ">
			¡Gracias por registrarse! Antes de comenzar, ¿puede verificar su dirección de correo electrónico haciendo clic en el
			enlace que le acabamos de enviar por correo electrónico? Si no recibiste el correo electrónico, con gusto te enviaremos otro.
			</div>

			{status === 'verification-link-sent' && (
				<div className="mb-4 font-medium text-sm text-blue-600">
					Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionó durante el registro.
				</div>
			)}

			<form onSubmit={submit}>
				<div className="mt-4 flex items-center justify-between">
					<Button processing={processing}>Reenviar correo electrónico de verificación</Button>

					<Link
						href={route('logout')}
						method="post"
						as="button"
						className="underline text-sm  hover:text-gray-200"
					>
						Cerrar Session
					</Link>
				</div>
			</form>
		</Guest>
	);
}
