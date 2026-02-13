--
-- PostgreSQL database dump
--

\restrict VRzDCN4etKAws5JgaKYmJb0vg7BIs6MUBWotfCxZZaAo9iAj6pnEJioh8GdbtfS

-- Dumped from database version 17.7
-- Dumped by pg_dump version 18.1

-- Started on 2026-02-13 15:00:44

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 236 (class 1255 OID 41075)
-- Name: fn_gen_asetkode(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_gen_asetkode() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_jeniskode text;
  v_yymm text;
  v_next int;
BEGIN
  IF NEW.asetkode IS NOT NULL AND btrim(NEW.asetkode) <> '' THEN
    RETURN NEW;
  END IF;

  SELECT jeniskode INTO v_jeniskode
  FROM public.jenis
  WHERE jenisid = NEW.jenisid;

  IF v_jeniskode IS NULL THEN
    RAISE EXCEPTION 'JenisID % tidak ditemukan, tidak bisa buat asetkode', NEW.jenisid;
  END IF;

  v_yymm := to_char(NEW.pembeliandate::date, 'YYYYMM');

  SELECT COALESCE(MAX((right(asetkode, 3))::int), 0) + 1
  INTO v_next
  FROM public.aset
  WHERE asetkode LIKE (v_jeniskode || '/' || v_yymm || '/%');

  NEW.asetkode := v_jeniskode || '/' || v_yymm || '/' || lpad(v_next::text, 3, '0');
  RETURN NEW;
END;
$$;


ALTER FUNCTION public.fn_gen_asetkode() OWNER TO postgres;

--
-- TOC entry 237 (class 1255 OID 41076)
-- Name: fn_gen_asetmoveno(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_gen_asetmoveno() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_lokasikode text;
  v_yymm text;
  v_next int;
BEGIN
  IF NEW.asetmoveno IS NOT NULL AND btrim(NEW.asetmoveno) <> '' THEN
    RETURN NEW;
  END IF;

  SELECT lokasikode INTO v_lokasikode
  FROM public.lokasi
  WHERE lokasiid = NEW.lokasiakhirid;

  IF v_lokasikode IS NULL THEN
    RAISE EXCEPTION 'LokasiAkhirID % tidak ditemukan, tidak bisa buat asetmoveno', NEW.lokasiakhirid;
  END IF;

  v_yymm := to_char(COALESCE(NEW.createddate, now())::date, 'YYYYMM');

  SELECT COALESCE(MAX((right(asetmoveno, 3))::int), 0) + 1
  INTO v_next
  FROM public.asetmove
  WHERE asetmoveno LIKE (v_lokasikode || '/' || v_yymm || '/%');

  NEW.asetmoveno := v_lokasikode || '/' || v_yymm || '/' || lpad(v_next::text, 3, '0');
  RETURN NEW;
END;
$$;


ALTER FUNCTION public.fn_gen_asetmoveno() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 217 (class 1259 OID 41077)
-- Name: aset; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.aset (
    asetid bigint NOT NULL,
    jenisid bigint NOT NULL,
    merkid bigint NOT NULL,
    lokasiid bigint NOT NULL,
    asetkode character varying(20) NOT NULL,
    pembeliandate date NOT NULL,
    pembelianno character varying(20) NOT NULL,
    isdeleted integer DEFAULT 0 NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp without time zone NOT NULL,
    updatedby bigint,
    updateddate timestamp without time zone,
    deletedby bigint,
    deleteddate timestamp without time zone
);


ALTER TABLE public.aset OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 41081)
-- Name: aset_asetid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.aset_asetid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.aset_asetid_seq OWNER TO postgres;

--
-- TOC entry 5035 (class 0 OID 0)
-- Dependencies: 218
-- Name: aset_asetid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.aset_asetid_seq OWNED BY public.aset.asetid;


--
-- TOC entry 219 (class 1259 OID 41083)
-- Name: asetmove; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asetmove (
    asetmoveid bigint NOT NULL,
    asetid bigint NOT NULL,
    asetmoveno character varying(20) NOT NULL,
    lokasiawalid bigint NOT NULL,
    lokasiakhirid bigint NOT NULL,
    isdeleted integer DEFAULT 0 NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp without time zone NOT NULL,
    updatedby bigint,
    updateddate timestamp without time zone,
    deletedby bigint,
    deleteddate timestamp without time zone,
    lokasiawal character varying(100),
    lokasiakhir character varying(100)
);


ALTER TABLE public.asetmove OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 41087)
-- Name: asetmove_asetmoveid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.asetmove_asetmoveid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.asetmove_asetmoveid_seq OWNER TO postgres;

--
-- TOC entry 5036 (class 0 OID 0)
-- Dependencies: 220
-- Name: asetmove_asetmoveid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asetmove_asetmoveid_seq OWNED BY public.asetmove.asetmoveid;


--
-- TOC entry 221 (class 1259 OID 41089)
-- Name: asetservice; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asetservice (
    asetserviceid bigint NOT NULL,
    asetid bigint NOT NULL,
    vendorid bigint NOT NULL,
    asetserviceno character varying(20) NOT NULL,
    asetservicedate date NOT NULL,
    remarks character varying(1000) NOT NULL,
    servicestatusid bigint NOT NULL,
    isdeleted integer NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp(0) without time zone NOT NULL,
    updateby bigint,
    updateddate timestamp(0) without time zone,
    deletedby bigint,
    deleteddate timestamp(0) without time zone
);


ALTER TABLE public.asetservice OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 41094)
-- Name: isdeleted; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.isdeleted (
    isdeleted bigint NOT NULL,
    status character varying(20) NOT NULL
);


ALTER TABLE public.isdeleted OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 41097)
-- Name: jenis; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jenis (
    jenisid bigint NOT NULL,
    jeniskode character varying(5) NOT NULL,
    jenis character varying(20) NOT NULL,
    isdeleted integer DEFAULT 0 NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp without time zone NOT NULL,
    updatedby bigint,
    updateddate timestamp without time zone,
    deletedby bigint,
    deleteddate timestamp without time zone
);


ALTER TABLE public.jenis OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 41101)
-- Name: jenis_jenisid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jenis_jenisid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jenis_jenisid_seq OWNER TO postgres;

--
-- TOC entry 5037 (class 0 OID 0)
-- Dependencies: 224
-- Name: jenis_jenisid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jenis_jenisid_seq OWNED BY public.jenis.jenisid;


--
-- TOC entry 225 (class 1259 OID 41103)
-- Name: lokasi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lokasi (
    lokasiid bigint NOT NULL,
    lokasikode character varying(5) NOT NULL,
    lokasi character varying(20) NOT NULL,
    isdeleted integer DEFAULT 0 NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp without time zone NOT NULL,
    updatedby bigint,
    updateddate timestamp without time zone,
    deletedby bigint,
    deleteddate timestamp without time zone
);


ALTER TABLE public.lokasi OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 41107)
-- Name: lokasi_lokasiid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lokasi_lokasiid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lokasi_lokasiid_seq OWNER TO postgres;

--
-- TOC entry 5038 (class 0 OID 0)
-- Dependencies: 226
-- Name: lokasi_lokasiid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lokasi_lokasiid_seq OWNED BY public.lokasi.lokasiid;


--
-- TOC entry 227 (class 1259 OID 41109)
-- Name: merk; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.merk (
    merkid bigint NOT NULL,
    merk character varying(20) NOT NULL,
    isdeleted integer DEFAULT 0 NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp without time zone NOT NULL,
    updatedby bigint,
    updateddate timestamp without time zone,
    deletedby bigint,
    deleteddate timestamp without time zone
);


ALTER TABLE public.merk OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 41113)
-- Name: merk_merkid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.merk_merkid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.merk_merkid_seq OWNER TO postgres;

--
-- TOC entry 5039 (class 0 OID 0)
-- Dependencies: 228
-- Name: merk_merkid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.merk_merkid_seq OWNED BY public.merk.merkid;


--
-- TOC entry 229 (class 1259 OID 41115)
-- Name: servicestatus; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.servicestatus (
    servicestatusid bigint NOT NULL,
    servicestatus character varying(20) NOT NULL
);


ALTER TABLE public.servicestatus OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 41118)
-- Name: syssetting; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.syssetting (
    syssettingid bigint NOT NULL,
    variable character varying(200) NOT NULL,
    value character varying(200) NOT NULL
);


ALTER TABLE public.syssetting OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 41121)
-- Name: user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."user" (
    userid bigint NOT NULL,
    username character varying(20) NOT NULL,
    nama character varying(100) NOT NULL,
    isdeleted integer DEFAULT 0 NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp without time zone NOT NULL,
    updatedby bigint,
    updateddate timestamp without time zone,
    deletedby bigint,
    deleteddate timestamp without time zone,
    password text NOT NULL,
    userlevelid bigint
);


ALTER TABLE public."user" OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 41127)
-- Name: user_userid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_userid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_userid_seq OWNER TO postgres;

--
-- TOC entry 5040 (class 0 OID 0)
-- Dependencies: 232
-- Name: user_userid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_userid_seq OWNED BY public."user".userid;


--
-- TOC entry 233 (class 1259 OID 41129)
-- Name: userlevel; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.userlevel (
    userlevelid bigint NOT NULL,
    userlevel character varying(255) NOT NULL
);


ALTER TABLE public.userlevel OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 41132)
-- Name: vendor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vendor (
    vendorid bigint NOT NULL,
    vendor character varying(100) NOT NULL,
    alamat character varying(300) NOT NULL,
    isdeleted integer NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp(0) without time zone NOT NULL,
    updatedby bigint,
    updateddate timestamp(0) without time zone,
    deletedby bigint,
    deleteddate timestamp(0) without time zone
);


ALTER TABLE public.vendor OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 41331)
-- Name: vendor_vendorid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.vendor ALTER COLUMN vendorid ADD GENERATED BY DEFAULT AS IDENTITY (
    SEQUENCE NAME public.vendor_vendorid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 4794 (class 2604 OID 41082)
-- Name: aset asetid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset ALTER COLUMN asetid SET DEFAULT nextval('public.aset_asetid_seq'::regclass);


--
-- TOC entry 4796 (class 2604 OID 41088)
-- Name: asetmove asetmoveid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove ALTER COLUMN asetmoveid SET DEFAULT nextval('public.asetmove_asetmoveid_seq'::regclass);


--
-- TOC entry 4798 (class 2604 OID 41102)
-- Name: jenis jenisid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis ALTER COLUMN jenisid SET DEFAULT nextval('public.jenis_jenisid_seq'::regclass);


--
-- TOC entry 4800 (class 2604 OID 41108)
-- Name: lokasi lokasiid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi ALTER COLUMN lokasiid SET DEFAULT nextval('public.lokasi_lokasiid_seq'::regclass);


--
-- TOC entry 4802 (class 2604 OID 41114)
-- Name: merk merkid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk ALTER COLUMN merkid SET DEFAULT nextval('public.merk_merkid_seq'::regclass);


--
-- TOC entry 4804 (class 2604 OID 41128)
-- Name: user userid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user" ALTER COLUMN userid SET DEFAULT nextval('public.user_userid_seq'::regclass);


--
-- TOC entry 5011 (class 0 OID 41077)
-- Dependencies: 217
-- Data for Name: aset; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.aset (asetid, jenisid, merkid, lokasiid, asetkode, pembeliandate, pembelianno, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
\.


--
-- TOC entry 5013 (class 0 OID 41083)
-- Dependencies: 219
-- Data for Name: asetmove; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asetmove (asetmoveid, asetid, asetmoveno, lokasiawalid, lokasiakhirid, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate, lokasiawal, lokasiakhir) FROM stdin;
\.


--
-- TOC entry 5015 (class 0 OID 41089)
-- Dependencies: 221
-- Data for Name: asetservice; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asetservice (asetserviceid, asetid, vendorid, asetserviceno, asetservicedate, remarks, servicestatusid, isdeleted, createdby, createddate, updateby, updateddate, deletedby, deleteddate) FROM stdin;
\.


--
-- TOC entry 5016 (class 0 OID 41094)
-- Dependencies: 222
-- Data for Name: isdeleted; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.isdeleted (isdeleted, status) FROM stdin;
0	AKTIF
1	TIDAK AKTIF
\.


--
-- TOC entry 5017 (class 0 OID 41097)
-- Dependencies: 223
-- Data for Name: jenis; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jenis (jenisid, jeniskode, jenis, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
1	PR	Printer	1	2	2026-02-13 03:13:32	2	2026-02-13 03:15:50	2	2026-02-13 03:15:28
2	MON	Monitor	0	2	2026-02-13 03:28:41	\N	\N	\N	\N
3	KEY	Keyboard	0	2	2026-02-13 03:29:00	2	2026-02-13 03:44:17	\N	\N
\.


--
-- TOC entry 5019 (class 0 OID 41103)
-- Dependencies: 225
-- Data for Name: lokasi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lokasi (lokasiid, lokasikode, lokasi, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
2	1LT2	Gedung 1 Lt2	0	2	2026-02-13 03:30:06	\N	\N	\N	\N
1	1LT1	Gedung 1 Lt1	0	2	2026-02-13 01:54:40	2	2026-02-13 03:30:19	\N	\N
4	1LT4	Gedung 1 Lt4	1	2	2026-02-13 03:31:27	\N	\N	2	2026-02-13 03:31:35
3	1LT3	Gedung 1 Lt3	1	2	2026-02-13 03:30:54	\N	\N	2	2026-02-13 03:31:58
\.


--
-- TOC entry 5021 (class 0 OID 41109)
-- Dependencies: 227
-- Data for Name: merk; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.merk (merkid, merk, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
1	Maspion	1	2	2026-02-12 05:31:11	\N	\N	2	2026-02-12 05:32:11
2	Panassonic	0	2	2026-02-13 01:53:31	\N	\N	\N	\N
\.


--
-- TOC entry 5023 (class 0 OID 41115)
-- Dependencies: 229
-- Data for Name: servicestatus; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.servicestatus (servicestatusid, servicestatus) FROM stdin;
0	MASUK SERVICE
1	PENGECEKAN
2	PENGECEKAN LANJUT
3	PERBAIKAN
4	PERBAIKAN LANJUT
5	SELESAI
98	BATAL SERVICE
99	BATAL RUSAK
\.


--
-- TOC entry 5024 (class 0 OID 41118)
-- Dependencies: 230
-- Data for Name: syssetting; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.syssetting (syssettingid, variable, value) FROM stdin;
1	DEFAULTUSERPASSWORD	Ekahusada-123
\.


--
-- TOC entry 5025 (class 0 OID 41121)
-- Dependencies: 231
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public."user" (userid, username, nama, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate, password, userlevelid) FROM stdin;
2	admin	Administrator	0	2	2026-02-12 12:20:45.992391	\N	\N	\N	\N	$2y$10$1got09WAEQzhWJp/DtPH3uycuehDnIRcwKnTLgBqylT9wH4FAGfBu	0
5	spradmn1	Achmad	0	2	2026-02-13 02:22:40	2	2026-02-13 02:23:12	\N	\N	$2y$12$f/4oArVFxL4WyQwpA2cuaOOoIuiwYhvm7SUWD2ZR57jPeGSBtVAsC	0
6	admin2	Fira Feri	0	2	2026-02-13 04:01:48	\N	\N	\N	\N	$2y$12$sZPVtA9SFHTIbEPbhQVizu5ybZAXMnWy.1m06vfMFvDBpks8dv1hy	0
\.


--
-- TOC entry 5027 (class 0 OID 41129)
-- Dependencies: 233
-- Data for Name: userlevel; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.userlevel (userlevelid, userlevel) FROM stdin;
0	SUPERADMIN
1	ADMIN
2	USER
99	GUEST
\.


--
-- TOC entry 5028 (class 0 OID 41132)
-- Dependencies: 234
-- Data for Name: vendor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vendor (vendorid, vendor, alamat, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
1	HOLINDO	Perak Surabaya	0	2	2026-02-13 06:51:25	\N	\N	\N	\N
2	HOLINDO	Surabaya	0	2	2026-02-13 06:56:25	\N	\N	\N	\N
3	dsad	dsadasd	0	2	2026-02-13 06:58:37	\N	\N	\N	\N
4	HOLINDO	Surabaya	0	2	2026-02-13 07:01:53	\N	\N	\N	\N
5	HOLINDO	Surabaya	0	2	2026-02-13 07:07:56	\N	\N	\N	\N
6	HOLINDO	Surabaya	0	2	2026-02-13 07:10:00	\N	\N	\N	\N
7	HOLINDO	sdmfwefiwewekonewge	0	2	2026-02-13 07:13:35	\N	\N	\N	\N
\.


--
-- TOC entry 5041 (class 0 OID 0)
-- Dependencies: 218
-- Name: aset_asetid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.aset_asetid_seq', 1, false);


--
-- TOC entry 5042 (class 0 OID 0)
-- Dependencies: 220
-- Name: asetmove_asetmoveid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asetmove_asetmoveid_seq', 1, false);


--
-- TOC entry 5043 (class 0 OID 0)
-- Dependencies: 224
-- Name: jenis_jenisid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jenis_jenisid_seq', 3, true);


--
-- TOC entry 5044 (class 0 OID 0)
-- Dependencies: 226
-- Name: lokasi_lokasiid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lokasi_lokasiid_seq', 4, true);


--
-- TOC entry 5045 (class 0 OID 0)
-- Dependencies: 228
-- Name: merk_merkid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.merk_merkid_seq', 2, true);


--
-- TOC entry 5046 (class 0 OID 0)
-- Dependencies: 232
-- Name: user_userid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_userid_seq', 6, true);


--
-- TOC entry 5047 (class 0 OID 0)
-- Dependencies: 235
-- Name: vendor_vendorid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.vendor_vendorid_seq', 7, true);


--
-- TOC entry 4807 (class 2606 OID 41136)
-- Name: aset aset_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT aset_pkey PRIMARY KEY (asetid);


--
-- TOC entry 4809 (class 2606 OID 41138)
-- Name: asetmove asetmove_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT asetmove_pkey PRIMARY KEY (asetmoveid);


--
-- TOC entry 4811 (class 2606 OID 41140)
-- Name: asetservice asetservice_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_pkey PRIMARY KEY (asetserviceid);


--
-- TOC entry 4813 (class 2606 OID 41142)
-- Name: isdeleted isdeleted_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.isdeleted
    ADD CONSTRAINT isdeleted_pkey PRIMARY KEY (isdeleted);


--
-- TOC entry 4815 (class 2606 OID 41144)
-- Name: jenis jenis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis
    ADD CONSTRAINT jenis_pkey PRIMARY KEY (jenisid);


--
-- TOC entry 4817 (class 2606 OID 41146)
-- Name: lokasi lokasi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi
    ADD CONSTRAINT lokasi_pkey PRIMARY KEY (lokasiid);


--
-- TOC entry 4819 (class 2606 OID 41148)
-- Name: merk merk_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk
    ADD CONSTRAINT merk_pkey PRIMARY KEY (merkid);


--
-- TOC entry 4821 (class 2606 OID 41150)
-- Name: servicestatus servicestatus_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.servicestatus
    ADD CONSTRAINT servicestatus_pkey PRIMARY KEY (servicestatusid);


--
-- TOC entry 4823 (class 2606 OID 41152)
-- Name: syssetting syssetting_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.syssetting
    ADD CONSTRAINT syssetting_pkey PRIMARY KEY (syssettingid);


--
-- TOC entry 4825 (class 2606 OID 41154)
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (userid);


--
-- TOC entry 4827 (class 2606 OID 41156)
-- Name: userlevel userlevel_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.userlevel
    ADD CONSTRAINT userlevel_pkey PRIMARY KEY (userlevelid);


--
-- TOC entry 4829 (class 2606 OID 41158)
-- Name: vendor vendor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_pkey PRIMARY KEY (vendorid);


--
-- TOC entry 4864 (class 2620 OID 41159)
-- Name: aset trg_gen_asetkode; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_gen_asetkode BEFORE INSERT ON public.aset FOR EACH ROW EXECUTE FUNCTION public.fn_gen_asetkode();


--
-- TOC entry 4865 (class 2620 OID 41160)
-- Name: asetmove trg_gen_asetmoveno; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_gen_asetmoveno BEFORE INSERT ON public.asetmove FOR EACH ROW EXECUTE FUNCTION public.fn_gen_asetmoveno();


--
-- TOC entry 4830 (class 2606 OID 41161)
-- Name: aset FK_aset_jenis; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_jenis" FOREIGN KEY (jenisid) REFERENCES public.jenis(jenisid);


--
-- TOC entry 4831 (class 2606 OID 41166)
-- Name: aset FK_aset_lokasi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_lokasi" FOREIGN KEY (lokasiid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4832 (class 2606 OID 41171)
-- Name: aset FK_aset_merk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_merk" FOREIGN KEY (merkid) REFERENCES public.merk(merkid);


--
-- TOC entry 4833 (class 2606 OID 41176)
-- Name: aset FK_aset_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4834 (class 2606 OID 41181)
-- Name: aset FK_aset_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4835 (class 2606 OID 41186)
-- Name: aset FK_aset_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4836 (class 2606 OID 41191)
-- Name: asetmove FK_asetmove_aset; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_aset" FOREIGN KEY (asetid) REFERENCES public.aset(asetid);


--
-- TOC entry 4837 (class 2606 OID 41196)
-- Name: asetmove FK_asetmove_lokasi_akhir; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_lokasi_akhir" FOREIGN KEY (lokasiakhirid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4838 (class 2606 OID 41201)
-- Name: asetmove FK_asetmove_lokasi_awal; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_lokasi_awal" FOREIGN KEY (lokasiawalid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4839 (class 2606 OID 41206)
-- Name: asetmove FK_asetmove_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4840 (class 2606 OID 41211)
-- Name: asetmove FK_asetmove_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4841 (class 2606 OID 41216)
-- Name: asetmove FK_asetmove_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4848 (class 2606 OID 41251)
-- Name: jenis FK_jenis_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis
    ADD CONSTRAINT "FK_jenis_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4849 (class 2606 OID 41256)
-- Name: jenis FK_jenis_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis
    ADD CONSTRAINT "FK_jenis_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4850 (class 2606 OID 41261)
-- Name: jenis FK_jenis_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis
    ADD CONSTRAINT "FK_jenis_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4851 (class 2606 OID 41266)
-- Name: lokasi FK_lokasi_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi
    ADD CONSTRAINT "FK_lokasi_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4852 (class 2606 OID 41271)
-- Name: lokasi FK_lokasi_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi
    ADD CONSTRAINT "FK_lokasi_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4853 (class 2606 OID 41276)
-- Name: lokasi FK_lokasi_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi
    ADD CONSTRAINT "FK_lokasi_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4854 (class 2606 OID 41281)
-- Name: merk FK_merk_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk
    ADD CONSTRAINT "FK_merk_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4855 (class 2606 OID 41286)
-- Name: merk FK_merk_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk
    ADD CONSTRAINT "FK_merk_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4856 (class 2606 OID 41291)
-- Name: merk FK_merk_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk
    ADD CONSTRAINT "FK_merk_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4857 (class 2606 OID 41296)
-- Name: user FK_user_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT "FK_user_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4858 (class 2606 OID 41301)
-- Name: user FK_user_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT "FK_user_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4859 (class 2606 OID 41306)
-- Name: user FK_user_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT "FK_user_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4842 (class 2606 OID 41221)
-- Name: asetservice asetservice_asetid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_asetid_foreign FOREIGN KEY (asetid) REFERENCES public.aset(asetid);


--
-- TOC entry 4843 (class 2606 OID 41226)
-- Name: asetservice asetservice_createdby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_createdby_foreign FOREIGN KEY (createdby) REFERENCES public."user"(userid);


--
-- TOC entry 4844 (class 2606 OID 41231)
-- Name: asetservice asetservice_deletedby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_deletedby_foreign FOREIGN KEY (deletedby) REFERENCES public."user"(userid);


--
-- TOC entry 4845 (class 2606 OID 41236)
-- Name: asetservice asetservice_servicestatusid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_servicestatusid_foreign FOREIGN KEY (servicestatusid) REFERENCES public.servicestatus(servicestatusid);


--
-- TOC entry 4846 (class 2606 OID 41241)
-- Name: asetservice asetservice_updateby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_updateby_foreign FOREIGN KEY (updateby) REFERENCES public."user"(userid);


--
-- TOC entry 4847 (class 2606 OID 41246)
-- Name: asetservice asetservice_vendorid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_vendorid_foreign FOREIGN KEY (vendorid) REFERENCES public.vendor(vendorid);


--
-- TOC entry 4860 (class 2606 OID 41311)
-- Name: user user_userlevelid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_userlevelid_foreign FOREIGN KEY (userlevelid) REFERENCES public.userlevel(userlevelid);


--
-- TOC entry 4861 (class 2606 OID 41316)
-- Name: vendor vendor_createdby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_createdby_foreign FOREIGN KEY (createdby) REFERENCES public."user"(userid);


--
-- TOC entry 4862 (class 2606 OID 41321)
-- Name: vendor vendor_deletedby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_deletedby_foreign FOREIGN KEY (deletedby) REFERENCES public."user"(userid);


--
-- TOC entry 4863 (class 2606 OID 41326)
-- Name: vendor vendor_updateby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_updateby_foreign FOREIGN KEY (updatedby) REFERENCES public."user"(userid);


-- Completed on 2026-02-13 15:00:47

--
-- PostgreSQL database dump complete
--

\unrestrict VRzDCN4etKAws5JgaKYmJb0vg7BIs6MUBWotfCxZZaAo9iAj6pnEJioh8GdbtfS

