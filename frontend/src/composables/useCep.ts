import { ref } from "vue";
import { getAddressByCep } from "@/api/viacep";
import { useToast } from "primevue/usetoast";

export function useCep(form: any) {
  const loadingCep = ref(false);
  const toast = useToast();

  async function fetchCep() {
    const cep = form.value.cep?.replace(/\D/g, "");
    if (!cep || cep.length !== 8) {
      toast.add({
        severity: "warn",
        summary: "CEP inválido",
        detail: "Digite um CEP válido de 8 dígitos.",
        life: 3000,
      });
      return;
    }

    loadingCep.value = true;
    const data = await getAddressByCep(cep);
    loadingCep.value = false;

    if (!data) {
      toast.add({
        severity: "error",
        summary: "Não encontrado",
        detail: "CEP não localizado no ViaCEP.",
        life: 3000,
      });
      return;
    }

    Object.assign(form.value, {
      estado: data.uf,
      cidade: data.localidade,
      bairro: data.bairro,
      endereco: data.logradouro,
    });
  }

  return { fetchCep, loadingCep };
}
