import { defineStore } from "pinia";
import { ContactsApi } from "./api";
import type { Contact } from "./types";

export const useContactsStore = defineStore("contacts", {
  state: () => ({
    items: [] as Contact[],
    loading: false,
    total: 0,
    currentPage: 1,
    perPage: 10,
  }),

  actions: {
    async fetchAll(params = { search: "", perPage: 10, page: 1 }) {
      this.loading = true;
      try {
        const perPage = params.perPage === 0 ? 9999 : params.perPage;
        const response = await ContactsApi.list({
          search: params.search,
          per_page: perPage,
          page: params.page,
        });

        this.items = response.data;
        this.total = response.total;
        this.currentPage = response.current_page;
        this.perPage = response.per_page;
      } finally {
        this.loading = false;
      }
    },

    async get(id: number) {
      this.loading = true;
      try {
        const data = await ContactsApi.get(id);
        return data;
      } finally {
        this.loading = false;
      }
    },

    async create(payload: Contact) {
      await ContactsApi.create(payload);
      await this.fetchAll();
    },

    async update(id: number, payload: Contact) {
      await ContactsApi.update(id, payload);
      await this.fetchAll();
    },

    async remove(id: number) {
      await ContactsApi.delete(id);
      this.items = this.items.filter((c) => c.id !== id);
    },
  },
});
