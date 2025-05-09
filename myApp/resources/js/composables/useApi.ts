import api from '@/services/api';
import type { AxiosRequestConfig } from 'axios';

export function useApi<T = any>() {
    const get = async (url: string, config?: AxiosRequestConfig) => {
      const response = await api.get<T>(url, config);
      return response.data;
    };
  
    const post = async (url: string, data: any, headers: Record<string, string> = {}) => {
      const response = await api.post<T>(url, data, {
        headers: {
          ...api.defaults.headers.common,
          ...headers,
        },
      });
      return response.data;
    };
    
    const put = async (url: string, data: any, headers: Record<string, string> = {}) => {
        const response = await api.put<T>(url, data, {
          headers: {
            ...api.defaults.headers.common,
            ...headers,
          },
        });
        return response.data;
      };
      

    const del = async (url: string) => {
      const response = await api.delete<T>(url);
      return response.data;
    };
  
    return { get, post, put, del };
  }
  