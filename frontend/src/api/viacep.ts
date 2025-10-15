import axios from "axios";

export interface AddressData {
  cep: string;
  logradouro: string;
  complemento: string;
  bairro: string;
  localidade: string;
  uf: string;
  ibge: string;
  gia: string;
  ddd: string;
  siafi: string;
}

export async function getAddressByCep(
  cep: string
): Promise<AddressData | null> {
  const cleanCep = cep.replace(/\D/g, "");

  if (cleanCep.length !== 8) return null;

  try {
    const { data } = await axios.get<AddressData>(
      `https://viacep.com.br/ws/${cleanCep}/json/`
    );

    if (data.erro) return null;
    return data;
  } catch (err) {
    console.error("Erro ao consultar ViaCEP:", err);
    return null;
  }
}
