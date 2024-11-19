import { createApi } from '@reduxjs/toolkit/query/react';
import customFetchBase from 'shared/api/customFetchBase';
import { parseJwt } from 'shared/lib/parseJwt/parseJwt';
import { isHaveAvatar } from 'shared/lib/isHaveAvatar/isHaveAvatar';
import { backgroundsClassesEmptyAvatar } from 'shared/const/backgroundsClassesEmptyAvatar';
import { blobToBase64 } from 'shared/lib/blobToBase64/blobToBase64';
import { IRequest, IResponse } from 'app/types/requests';
import { decryptAesJson } from 'app/providers/KeysProvider/model/lib/cryptoService';
import { removeAccessToken } from 'shared/lib/localStorage';
import { IUser } from '../model/types/User';
import { setAuthData, setUser, setImageAvatar } from '../model/slice/userSlice';

export const userApi = createApi({
    reducerPath: 'userApi',
    baseQuery: customFetchBase,
    tagTypes: ['User', 'ChangeName'],
    endpoints: (builder) => ({
        getUser: builder.query<IResponse, null>({
            query() {
                return {
                    url: 'user/getMe',
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                    },
                };
            },
            providesTags: ['User'],
            async onQueryStarted(args, { dispatch, queryFulfilled }) {
                try {
                    const { data } = await queryFulfilled;

                    const decryptedData:IUser = decryptAesJson(data.data) as IUser;

                    const authData = parseJwt(localStorage.getItem('access_token'));

                    const isAvatar = isHaveAvatar(backgroundsClassesEmptyAvatar, decryptedData.path_avatar);

                    if (isAvatar) {
                        const { data: avatar } = await dispatch(userApi.endpoints.getAvatar.initiate(null));
                        if (avatar) {
                            const base64 = await blobToBase64(avatar) as string | ArrayBuffer;

                            dispatch(setImageAvatar(base64));
                        }
                    }
                    dispatch(setAuthData(authData));
                    dispatch(setUser(decryptedData));
                } catch (error) {
                    console.log(error);
                }
            },
        }),
        changeAvatar: builder.mutation<IUser, { path: string } | FormData>({
            query(data) {
                return {
                    url: 'user/editUserAvatar',
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                    body: data,
                };
            },
            async onQueryStarted(args, { dispatch, queryFulfilled }) {
                try {
                    const { data } = await queryFulfilled;
                    dispatch(setUser(data));
                } catch (error) {
                    console.log(error);
                }
            },
        }),
        uploadAvatar: builder.mutation<Blob, FormData>({
            query(data) {
                return {
                    url: 'user/uploadAvatar',
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                    body: data,
                    responseHandler: (response: any) => response.blob(),
                };
            },
            async onQueryStarted(args, { dispatch, queryFulfilled }) {
                try {
                    const { data } = await queryFulfilled;

                    if (data) {
                        const base64 = await blobToBase64(data) as string | ArrayBuffer;

                        dispatch(setImageAvatar(base64));
                    }
                } catch (error) {
                    console.log(error);
                }
            },

        }),
        changeDescription: builder.mutation<IResponse, IRequest | null>({
            query(data) {
                return {
                    url: 'user/editUserDescription',
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                    body: data,
                };
            },
            async onQueryStarted(args, { dispatch, queryFulfilled }) {
                try {
                    const { data } = await queryFulfilled;
                    const decryptedData:IUser = decryptAesJson(data.data) as IUser;

                    dispatch(setUser(decryptedData));
                } catch (error) {
                    console.log(error);
                }
            },
        }),
        changeName: builder.mutation<IResponse, IRequest>({
            query(data) {
                return {
                    url: 'user/editUserName',
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                    body: data,
                };
            },
            async onQueryStarted(args, { dispatch, queryFulfilled }) {
                try {
                    const { data } = await queryFulfilled;
                    const decryptedData:IUser = decryptAesJson(data.data) as IUser;

                    dispatch(setUser(decryptedData));
                } catch (error) {
                    console.log(error);
                }
            },
        }),
        changeLogin: builder.mutation<IResponse, IRequest>({
            query(data) {
                return {
                    url: 'user/editUserLogin',
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                    body: data,
                };
            },
            async onQueryStarted(args, { dispatch, queryFulfilled }) {
                try {
                    const { data } = await queryFulfilled;
                    const decryptedData:IUser = decryptAesJson(data.data) as IUser;

                    dispatch(setUser(decryptedData));
                } catch (error) {
                    console.log(error);
                }
            },
        }),
        changeEmail: builder.mutation<IResponse, IRequest>({
            query(data) {
                return {
                    url: 'user/editUserEmail',
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                    body: data,
                };
            },
            async onQueryStarted(args, { dispatch, queryFulfilled }) {
                try {
                    const { data } = await queryFulfilled;
                    const decryptedData:IUser = decryptAesJson(data.data) as IUser;

                    dispatch(setUser(decryptedData));
                } catch (error) {
                    console.log(error);
                }
            },
        }),
        logoutUser: builder.mutation<null, null>({
            query() {
                return {
                    url: 'auth/logout',
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Public-req': 'public'
                    },
                    credentials: 'include',
                };
            },
            invalidatesTags: ['User'],
            async onQueryStarted(args, { dispatch, queryFulfilled }) {
                const { data } = await queryFulfilled;

                if (data === null) {
                    removeAccessToken();

                }
            },
        }),
        getAvatar: builder.query<Blob, null>({
            query() {
                return {
                    url: 'user/getAvatar',
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                    },
                    responseHandler: (response: any) => response.blob(),
                };
            },
        }),
        getAvatarFromLogin: builder.query<Blob, { login: string }>({
            query(data) {
                return {
                    url: 'user/getAvatarFromLogin',
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                    },
                    params: data,
                    responseHandler: (response: any) => response.blob(),
                };
            },
        }),
    }),
});

export const {
    useGetUserQuery,
    useChangeAvatarMutation,
    useChangeNameMutation,
    useLazyGetAvatarQuery,
    useLazyGetAvatarFromLoginQuery,
    useUploadAvatarMutation,
    useChangeLoginMutation,
    useChangeEmailMutation,
    useLogoutUserMutation,
    useChangeDescriptionMutation,
} = userApi;
