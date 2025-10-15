import api from "@/api/axios";
import type { Contact } from "./types";

export interface ContactListParams {
  page?: number;
  per_page?: number;
  search?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  current_page: number;
  per_page: number;
}

export const ContactsApi = {
  list: async (
    params?: ContactListParams
  ): Promise<PaginatedResponse<Contact>> => {
    const { data } = await api.get("/contacts", { params });
    return data;
  },

  get: async (id: number): Promise<Contact> => {
    const { data } = await api.get(`/contacts/${id}`);
    return data;
  },

  create: async (payload: Contact): Promise<Contact> => {
    const { data } = await api.post("/contacts", payload);
    return data;
  },

  update: async (id: number, payload: Contact): Promise<Contact> => {
    const { data } = await api.put(`/contacts/${id}`, payload);
    return data;
  },

  delete: async (id: number): Promise<void> => {
    await api.delete(`/contacts/${id}`);
  },
};
