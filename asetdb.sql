--
-- PostgreSQL database dump
--

\restrict 1j0HSqlbeRUNNhDRExhgi0y3Ijefbanp0EhoMQ5r7XWyMxSMVFOu5g9GCTkJhlw

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-02-26 06:35:05

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
-- TOC entry 243 (class 1255 OID 18064)
-- Name: fn_asetservice_sync_aset_lokasi(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_asetservice_sync_aset_lokasi() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  v_vendor_lokasiid bigint;
  v_aset_lokasiid bigint;
BEGIN
  SELECT lokasiid INTO v_vendor_lokasiid
  FROM public.vendor
  WHERE vendorid = NEW.vendorid;

  SELECT lokasiid INTO v_aset_lokasiid
  FROM public.aset
  WHERE asetid = NEW.asetid;

  -- status proses: 0..4
  IF NEW.servicestatusid IN (0,1,2,3,4) THEN
    IF v_vendor_lokasiid IS NULL THEN
      RAISE EXCEPTION 'VendorID % belum punya lokasiid', NEW.vendorid;
    END IF;

    IF NEW.lokasisebelumserviceid IS NULL THEN
      NEW.lokasisebelumserviceid := v_aset_lokasiid;
    END IF;

    UPDATE public.aset
    SET lokasiid = v_vendor_lokasiid,
        updatedby = COALESCE(NEW.updateby, NEW.createdby),
        updateddate = now()
    WHERE asetid = NEW.asetid;

    RETURN NEW;
  END IF;

  -- status final: 5/98/99
  IF NEW.servicestatusid IN (5,98,99) THEN
    IF NEW.lokasisebelumserviceid IS NULL THEN
      RAISE EXCEPTION 'lokasisebelumserviceid NULL untuk asetserviceid %', NEW.asetserviceid;
    END IF;

    UPDATE public.aset
    SET lokasiid = NEW.lokasisebelumserviceid,
        updatedby = COALESCE(NEW.updateby, NEW.createdby),
        updateddate = now()
    WHERE asetid = NEW.asetid;

    RETURN NEW;
  END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION public.fn_asetservice_sync_aset_lokasi() OWNER TO postgres;

--
-- TOC entry 241 (class 1255 OID 18044)
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
  -- kalau asetkode sudah diisi manual, biarkan
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
-- TOC entry 242 (class 1255 OID 18046)
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
-- TOC entry 228 (class 1259 OID 17819)
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
-- TOC entry 227 (class 1259 OID 17818)
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
-- TOC entry 5177 (class 0 OID 0)
-- Dependencies: 227
-- Name: aset_asetid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.aset_asetid_seq OWNED BY public.aset.asetid;


--
-- TOC entry 230 (class 1259 OID 17866)
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
-- TOC entry 229 (class 1259 OID 17865)
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
-- TOC entry 5178 (class 0 OID 0)
-- Dependencies: 229
-- Name: asetmove_asetmoveid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asetmove_asetmoveid_seq OWNED BY public.asetmove.asetmoveid;


--
-- TOC entry 236 (class 1259 OID 17967)
-- Name: asetservice; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asetservice (
    asetserviceid bigint NOT NULL,
    asetid bigint NOT NULL,
    vendorid bigint NOT NULL,
    remarks character varying(1000) NOT NULL,
    servicestatusid bigint NOT NULL,
    isdeleted integer NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp(0) without time zone NOT NULL,
    updateby bigint,
    updateddate timestamp(0) without time zone,
    deletedby bigint,
    deleteddate timestamp(0) without time zone,
    lokasisebelumserviceid bigint
);


ALTER TABLE public.asetservice OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 18052)
-- Name: asetservice_asetserviceid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.asetservice_asetserviceid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.asetservice_asetserviceid_seq OWNER TO postgres;

--
-- TOC entry 5179 (class 0 OID 0)
-- Dependencies: 238
-- Name: asetservice_asetserviceid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asetservice_asetserviceid_seq OWNED BY public.asetservice.asetserviceid;


--
-- TOC entry 239 (class 1259 OID 18067)
-- Name: asetservicedt; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asetservicedt (
    asetservicedtid bigint NOT NULL,
    asetserviceid bigint NOT NULL,
    asetserviceno character varying(50) NOT NULL,
    asetservicedate date NOT NULL,
    isdeleted integer DEFAULT 0 NOT NULL,
    createdby bigint NOT NULL,
    createddate timestamp without time zone NOT NULL,
    updatedby bigint,
    updateddate timestamp without time zone,
    deletedby bigint,
    deleteddate timestamp without time zone
);


ALTER TABLE public.asetservicedt OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 18078)
-- Name: asetservicedt_asetservicedtid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.asetservicedt_asetservicedtid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.asetservicedt_asetservicedtid_seq OWNER TO postgres;

--
-- TOC entry 5180 (class 0 OID 0)
-- Dependencies: 240
-- Name: asetservicedt_asetservicedtid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.asetservicedt_asetservicedtid_seq OWNED BY public.asetservicedt.asetservicedtid;


--
-- TOC entry 234 (class 1259 OID 17952)
-- Name: isdeleted; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.isdeleted (
    isdeleted bigint NOT NULL,
    status character varying(20) NOT NULL
);


ALTER TABLE public.isdeleted OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 17736)
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
-- TOC entry 221 (class 1259 OID 17735)
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
-- TOC entry 5181 (class 0 OID 0)
-- Dependencies: 221
-- Name: jenis_jenisid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jenis_jenisid_seq OWNED BY public.jenis.jenisid;


--
-- TOC entry 224 (class 1259 OID 17764)
-- Name: lokasi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lokasi (
    lokasiid bigint NOT NULL,
    lokasikode character varying(5) NOT NULL,
    lokasi character varying(100) NOT NULL,
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
-- TOC entry 223 (class 1259 OID 17763)
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
-- TOC entry 5182 (class 0 OID 0)
-- Dependencies: 223
-- Name: lokasi_lokasiid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lokasi_lokasiid_seq OWNED BY public.lokasi.lokasiid;


--
-- TOC entry 226 (class 1259 OID 17792)
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
-- TOC entry 225 (class 1259 OID 17791)
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
-- TOC entry 5183 (class 0 OID 0)
-- Dependencies: 225
-- Name: merk_merkid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.merk_merkid_seq OWNED BY public.merk.merkid;


--
-- TOC entry 233 (class 1259 OID 17945)
-- Name: servicestatus; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.servicestatus (
    servicestatusid bigint NOT NULL,
    servicestatus character varying(20) NOT NULL
);


ALTER TABLE public.servicestatus OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 17959)
-- Name: syssetting; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.syssetting (
    syssettingid bigint NOT NULL,
    variable character varying(200) NOT NULL,
    value character varying(200) NOT NULL
);


ALTER TABLE public.syssetting OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 17708)
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
-- TOC entry 219 (class 1259 OID 17707)
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
-- TOC entry 5184 (class 0 OID 0)
-- Dependencies: 219
-- Name: user_userid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_userid_seq OWNED BY public."user".userid;


--
-- TOC entry 231 (class 1259 OID 17927)
-- Name: userlevel; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.userlevel (
    userlevelid bigint NOT NULL,
    userlevel character varying(255) NOT NULL
);


ALTER TABLE public.userlevel OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 17934)
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
    deleteddate timestamp(0) without time zone,
    lokasiid bigint
);


ALTER TABLE public.vendor OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 18050)
-- Name: vendor_vendorid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.vendor_vendorid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.vendor_vendorid_seq OWNER TO postgres;

--
-- TOC entry 5185 (class 0 OID 0)
-- Dependencies: 237
-- Name: vendor_vendorid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.vendor_vendorid_seq OWNED BY public.vendor.vendorid;


--
-- TOC entry 4923 (class 2604 OID 17822)
-- Name: aset asetid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset ALTER COLUMN asetid SET DEFAULT nextval('public.aset_asetid_seq'::regclass);


--
-- TOC entry 4925 (class 2604 OID 17869)
-- Name: asetmove asetmoveid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove ALTER COLUMN asetmoveid SET DEFAULT nextval('public.asetmove_asetmoveid_seq'::regclass);


--
-- TOC entry 4928 (class 2604 OID 18053)
-- Name: asetservice asetserviceid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice ALTER COLUMN asetserviceid SET DEFAULT nextval('public.asetservice_asetserviceid_seq'::regclass);


--
-- TOC entry 4929 (class 2604 OID 18079)
-- Name: asetservicedt asetservicedtid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservicedt ALTER COLUMN asetservicedtid SET DEFAULT nextval('public.asetservicedt_asetservicedtid_seq'::regclass);


--
-- TOC entry 4917 (class 2604 OID 17739)
-- Name: jenis jenisid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis ALTER COLUMN jenisid SET DEFAULT nextval('public.jenis_jenisid_seq'::regclass);


--
-- TOC entry 4919 (class 2604 OID 17767)
-- Name: lokasi lokasiid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi ALTER COLUMN lokasiid SET DEFAULT nextval('public.lokasi_lokasiid_seq'::regclass);


--
-- TOC entry 4921 (class 2604 OID 17795)
-- Name: merk merkid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk ALTER COLUMN merkid SET DEFAULT nextval('public.merk_merkid_seq'::regclass);


--
-- TOC entry 4915 (class 2604 OID 17711)
-- Name: user userid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user" ALTER COLUMN userid SET DEFAULT nextval('public.user_userid_seq'::regclass);


--
-- TOC entry 4927 (class 2604 OID 18051)
-- Name: vendor vendorid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor ALTER COLUMN vendorid SET DEFAULT nextval('public.vendor_vendorid_seq'::regclass);


--
-- TOC entry 5159 (class 0 OID 17819)
-- Dependencies: 228
-- Data for Name: aset; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.aset (asetid, jenisid, merkid, lokasiid, asetkode, pembeliandate, pembelianno, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
2	4	5	4	MON/202602/001	2026-02-20	INV0121232	0	3	2026-02-20 07:05:11	2	2026-02-25 08:38:34	\N	\N
3	3	6	7	PRI/202602/001	2026-02-25	INV0121233	0	2	2026-02-25 08:37:49	2	2026-02-25 16:14:04.01644	\N	\N
1	2	3	12	KEY/202602/001	2026-02-08	INV0121231	0	3	2026-02-19 02:20:39	2	2026-02-25 16:14:42.731654	\N	\N
4	4	8	10	MON/202602/002	2026-02-01	INV0121234	0	2	2026-02-25 23:29:11	2	2026-02-26 06:30:48.07383	\N	\N
\.


--
-- TOC entry 5161 (class 0 OID 17866)
-- Dependencies: 230
-- Data for Name: asetmove; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asetmove (asetmoveid, asetid, asetmoveno, lokasiawalid, lokasiakhirid, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate, lokasiawal, lokasiakhir) FROM stdin;
1	1	1LT1/202602/001	2	1	0	3	2026-02-19 03:38:56	\N	\N	\N	\N	\N	\N
2	2	1LT3/202602/001	1	3	0	3	2026-02-20 07:13:42	\N	\N	\N	\N	\N	\N
3	2	1LT2/202602/001	3	2	0	3	2026-02-20 07:16:57	\N	\N	\N	\N	\N	\N
4	2	ADM/202602/001	2	6	0	2	2026-02-25 08:38:34	\N	\N	\N	\N	\N	\N
5	3	FAR/202602/001	4	7	0	2	2026-02-25 08:42:56	\N	\N	\N	\N	\N	\N
\.


--
-- TOC entry 5167 (class 0 OID 17967)
-- Dependencies: 236
-- Data for Name: asetservice; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asetservice (asetserviceid, asetid, vendorid, remarks, servicestatusid, isdeleted, createdby, createddate, updateby, updateddate, deletedby, deleteddate, lokasisebelumserviceid) FROM stdin;
2	2	2	perbaikan monitor	1	0	3	2026-02-20 07:09:04	3	2026-02-20 07:09:54	\N	\N	\N
1	1	3	INV0121231	4	0	3	2026-02-19 02:56:04	2	2026-02-25 09:14:42	\N	\N	4
3	3	1	tinta bocor	5	1	2	2026-02-25 08:53:04	\N	\N	2	2026-02-25 23:31:07	7
4	4	1	glitch	3	1	2	2026-02-25 23:30:11	2	2026-02-25 23:30:48	2	2026-02-25 23:31:41	6
\.


--
-- TOC entry 5170 (class 0 OID 18067)
-- Dependencies: 239
-- Data for Name: asetservicedt; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asetservicedt (asetservicedtid, asetserviceid, asetserviceno, asetservicedate, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
1	2	INV 003	2026-02-20	0	3	2026-02-20 07:09:04	3	2026-02-20 07:09:54	\N	\N
3	1	INV0121231	2026-02-15	0	3	2026-02-19 02:56:04	2	2026-02-25 09:14:42	\N	\N
2	3	INV 004	2026-02-25	1	2	2026-02-25 08:53:04	\N	\N	2	2026-02-25 23:31:07
4	4	INV 005	2026-02-23	1	2	2026-02-25 23:30:11	2	2026-02-25 23:30:48	2	2026-02-25 23:31:41
\.


--
-- TOC entry 5165 (class 0 OID 17952)
-- Dependencies: 234
-- Data for Name: isdeleted; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.isdeleted (isdeleted, status) FROM stdin;
0	AKTIF
1	TIDAK AKTIF
\.


--
-- TOC entry 5153 (class 0 OID 17736)
-- Dependencies: 222
-- Data for Name: jenis; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jenis (jenisid, jeniskode, jenis, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
2	KEY	Keyboard	0	3	2026-02-19 02:19:50	\N	\N	\N	\N
1	001	Elektronik	1	3	2026-02-19 02:17:31	\N	\N	3	2026-02-19 02:19:56
3	PRI	Printer	0	3	2026-02-19 02:51:43	\N	\N	\N	\N
4	MON	Monitor	0	3	2026-02-19 02:51:54	\N	\N	\N	\N
\.


--
-- TOC entry 5155 (class 0 OID 17764)
-- Dependencies: 224
-- Data for Name: lokasi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lokasi (lokasiid, lokasikode, lokasi, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
4	IGD	IGD	0	2	2026-02-25 15:35:36.816789	\N	\N	\N	\N
5	POL	POLI	0	2	2026-02-25 15:35:36.816789	\N	\N	\N	\N
6	ADM	ADMISI	0	2	2026-02-25 15:35:36.816789	\N	\N	\N	\N
7	FAR	FARMASI	0	2	2026-02-25 15:35:36.816789	\N	\N	\N	\N
8	LAB	LAB	0	2	2026-02-25 15:35:36.816789	\N	\N	\N	\N
9	RAD	RADIOLOGI	0	2	2026-02-25 15:35:36.816789	\N	\N	\N	\N
1	1LT1	Gedung 1 LT 1	1	2	2026-02-12 10:08:12	\N	\N	2	2026-02-25 08:40:25
2	1LT2	Gedung 1 LT 2	1	2	2026-02-12 10:08:46	\N	\N	2	2026-02-25 08:40:27
3	1LT3	Gedung 1 LT 3	1	2	2026-02-12 10:09:07	\N	\N	2	2026-02-25 08:40:30
10	V01	VENDOR - TEST	0	2	2026-02-25 16:09:12.984095	\N	\N	\N	\N
11	V02	VENDOR - Hartono - Bukit Darmo	0	2	2026-02-25 16:09:12.984095	\N	\N	\N	\N
12	V03	VENDOR - SOLINDO	0	2	2026-02-25 16:09:12.984095	\N	\N	\N	\N
\.


--
-- TOC entry 5157 (class 0 OID 17792)
-- Dependencies: 226
-- Data for Name: merk; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.merk (merkid, merk, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate) FROM stdin;
2	Panasonic	0	2	2026-02-12 10:37:56	\N	\N	\N	\N
1	Maspion	0	2	2026-02-12 05:31:11	2	2026-02-12 10:43:49	\N	\N
3	LOGITECH	0	3	2026-02-19 02:19:11	\N	\N	\N	\N
4	Belden	0	3	2026-02-19 02:51:16	\N	\N	\N	\N
5	LG	0	3	2026-02-19 02:51:20	\N	\N	\N	\N
6	Epson	0	2	2026-02-25 08:37:19	\N	\N	\N	\N
7	Samsung	0	2	2026-02-25 23:28:15	\N	\N	\N	\N
8	Lenovo	0	2	2026-02-25 23:28:28	\N	\N	\N	\N
\.


--
-- TOC entry 5164 (class 0 OID 17945)
-- Dependencies: 233
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
-- TOC entry 5166 (class 0 OID 17959)
-- Dependencies: 235
-- Data for Name: syssetting; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.syssetting (syssettingid, variable, value) FROM stdin;
1	DEFAULTUSERPASSWORD	Ekahusada-123
\.


--
-- TOC entry 5151 (class 0 OID 17708)
-- Dependencies: 220
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public."user" (userid, username, nama, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate, password, userlevelid) FROM stdin;
3	admin1	fafa	1	2	2026-02-12 11:01:52	3	2026-02-25 07:35:03	3	2026-02-25 07:46:47	$2y$10$7xsmyFmnSemnSZl5.FTmiOxQvR.uKavTFOafw3wESMBX9pPjVuQ.S	0
2	admin	admin	0	2	2026-02-12 12:20:45.992391	3	2026-02-25 07:53:15	\N	\N	$2y$10$jflFujabTJM/CDf0Rt/DyeMIEQZabVpxTCgzh8lnkv0pDlkWx8mr2	0
5	user	user	0	3	2026-02-25 08:13:35	\N	\N	\N	\N	$2y$10$p7JVLkkpc7mpKHlMm9S3z.CiO/h6MQUzxsfnhLJc5wVrDeLw/Ylkq	2
4	admin2	admin2	0	3	2026-02-25 07:35:46	2	2026-02-25 08:14:52	\N	\N	$2y$10$0gRFQ2u7Le2fqYPVL4y9LORzKrJGm5.3s.roktoFErPkjPQMWg.Dq	1
\.


--
-- TOC entry 5162 (class 0 OID 17927)
-- Dependencies: 231
-- Data for Name: userlevel; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.userlevel (userlevelid, userlevel) FROM stdin;
0	Superadmin
1	Admin
99	Guest
2	User
\.


--
-- TOC entry 5163 (class 0 OID 17934)
-- Dependencies: 232
-- Data for Name: vendor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vendor (vendorid, vendor, alamat, isdeleted, createdby, createddate, updatedby, updateddate, deletedby, deleteddate, lokasiid) FROM stdin;
1	TEST	TEST	0	2	2026-02-16 07:53:34	\N	\N	\N	\N	10
2	Hartono - Bukit Darmo	Yono Suwoyo, Jl. Mayjend. Jonosewojo No.12, Pradahkalikendal, Kec. Dukuhpakis, Surabaya, Jawa Timur 60226	0	3	2026-02-19 02:16:08	\N	\N	\N	\N	11
3	SOLINDO	Perak Surabaya	0	3	2026-02-19 02:53:21	\N	\N	\N	\N	12
4	IHS	Mayjend Sungkono	1	3	2026-02-19 02:53:41	\N	\N	2	2026-02-25 23:32:19	\N
5	3 DIAMOND	ITC Surabaya	1	3	2026-02-19 02:54:00	\N	\N	2	2026-02-25 23:32:23	\N
\.


--
-- TOC entry 5186 (class 0 OID 0)
-- Dependencies: 227
-- Name: aset_asetid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.aset_asetid_seq', 4, true);


--
-- TOC entry 5187 (class 0 OID 0)
-- Dependencies: 229
-- Name: asetmove_asetmoveid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asetmove_asetmoveid_seq', 5, true);


--
-- TOC entry 5188 (class 0 OID 0)
-- Dependencies: 238
-- Name: asetservice_asetserviceid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asetservice_asetserviceid_seq', 4, true);


--
-- TOC entry 5189 (class 0 OID 0)
-- Dependencies: 240
-- Name: asetservicedt_asetservicedtid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.asetservicedt_asetservicedtid_seq', 4, true);


--
-- TOC entry 5190 (class 0 OID 0)
-- Dependencies: 221
-- Name: jenis_jenisid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jenis_jenisid_seq', 4, true);


--
-- TOC entry 5191 (class 0 OID 0)
-- Dependencies: 223
-- Name: lokasi_lokasiid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lokasi_lokasiid_seq', 12, true);


--
-- TOC entry 5192 (class 0 OID 0)
-- Dependencies: 225
-- Name: merk_merkid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.merk_merkid_seq', 8, true);


--
-- TOC entry 5193 (class 0 OID 0)
-- Dependencies: 219
-- Name: user_userid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_userid_seq', 5, true);


--
-- TOC entry 5194 (class 0 OID 0)
-- Dependencies: 237
-- Name: vendor_vendorid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.vendor_vendorid_seq', 5, true);


--
-- TOC entry 4940 (class 2606 OID 17834)
-- Name: aset aset_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT aset_pkey PRIMARY KEY (asetid);


--
-- TOC entry 4942 (class 2606 OID 17879)
-- Name: asetmove asetmove_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT asetmove_pkey PRIMARY KEY (asetmoveid);


--
-- TOC entry 4954 (class 2606 OID 17983)
-- Name: asetservice asetservice_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_pkey PRIMARY KEY (asetserviceid);


--
-- TOC entry 4956 (class 2606 OID 18081)
-- Name: asetservicedt asetservicedt_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservicedt
    ADD CONSTRAINT asetservicedt_pkey PRIMARY KEY (asetservicedtid);


--
-- TOC entry 4950 (class 2606 OID 17958)
-- Name: isdeleted isdeleted_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.isdeleted
    ADD CONSTRAINT isdeleted_pkey PRIMARY KEY (isdeleted);


--
-- TOC entry 4934 (class 2606 OID 17747)
-- Name: jenis jenis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis
    ADD CONSTRAINT jenis_pkey PRIMARY KEY (jenisid);


--
-- TOC entry 4936 (class 2606 OID 17775)
-- Name: lokasi lokasi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi
    ADD CONSTRAINT lokasi_pkey PRIMARY KEY (lokasiid);


--
-- TOC entry 4938 (class 2606 OID 17802)
-- Name: merk merk_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk
    ADD CONSTRAINT merk_pkey PRIMARY KEY (merkid);


--
-- TOC entry 4948 (class 2606 OID 17951)
-- Name: servicestatus servicestatus_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.servicestatus
    ADD CONSTRAINT servicestatus_pkey PRIMARY KEY (servicestatusid);


--
-- TOC entry 4952 (class 2606 OID 17966)
-- Name: syssetting syssetting_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.syssetting
    ADD CONSTRAINT syssetting_pkey PRIMARY KEY (syssettingid);


--
-- TOC entry 4932 (class 2606 OID 17719)
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (userid);


--
-- TOC entry 4944 (class 2606 OID 17933)
-- Name: userlevel userlevel_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.userlevel
    ADD CONSTRAINT userlevel_pkey PRIMARY KEY (userlevelid);


--
-- TOC entry 4946 (class 2606 OID 17944)
-- Name: vendor vendor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_pkey PRIMARY KEY (vendorid);


--
-- TOC entry 4957 (class 1259 OID 18102)
-- Name: idx_asetservicedt_asetserviceid; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_asetservicedt_asetserviceid ON public.asetservicedt USING btree (asetserviceid);


--
-- TOC entry 5002 (class 2620 OID 18065)
-- Name: asetservice trg_asetservice_sync_aset_lokasi; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_asetservice_sync_aset_lokasi BEFORE INSERT OR UPDATE OF servicestatusid, vendorid ON public.asetservice FOR EACH ROW EXECUTE FUNCTION public.fn_asetservice_sync_aset_lokasi();


--
-- TOC entry 5000 (class 2620 OID 18045)
-- Name: aset trg_gen_asetkode; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_gen_asetkode BEFORE INSERT ON public.aset FOR EACH ROW EXECUTE FUNCTION public.fn_gen_asetkode();


--
-- TOC entry 5001 (class 2620 OID 18047)
-- Name: asetmove trg_gen_asetmoveno; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_gen_asetmoveno BEFORE INSERT ON public.asetmove FOR EACH ROW EXECUTE FUNCTION public.fn_gen_asetmoveno();


--
-- TOC entry 4971 (class 2606 OID 17835)
-- Name: aset FK_aset_jenis; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_jenis" FOREIGN KEY (jenisid) REFERENCES public.jenis(jenisid);


--
-- TOC entry 4972 (class 2606 OID 17845)
-- Name: aset FK_aset_lokasi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_lokasi" FOREIGN KEY (lokasiid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4973 (class 2606 OID 17840)
-- Name: aset FK_aset_merk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_merk" FOREIGN KEY (merkid) REFERENCES public.merk(merkid);


--
-- TOC entry 4974 (class 2606 OID 17850)
-- Name: aset FK_aset_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4975 (class 2606 OID 17860)
-- Name: aset FK_aset_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4976 (class 2606 OID 17855)
-- Name: aset FK_aset_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.aset
    ADD CONSTRAINT "FK_aset_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4977 (class 2606 OID 17880)
-- Name: asetmove FK_asetmove_aset; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_aset" FOREIGN KEY (asetid) REFERENCES public.aset(asetid);


--
-- TOC entry 4978 (class 2606 OID 17890)
-- Name: asetmove FK_asetmove_lokasi_akhir; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_lokasi_akhir" FOREIGN KEY (lokasiakhirid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4979 (class 2606 OID 17885)
-- Name: asetmove FK_asetmove_lokasi_awal; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_lokasi_awal" FOREIGN KEY (lokasiawalid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4980 (class 2606 OID 17895)
-- Name: asetmove FK_asetmove_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4981 (class 2606 OID 17905)
-- Name: asetmove FK_asetmove_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4982 (class 2606 OID 17900)
-- Name: asetmove FK_asetmove_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT "FK_asetmove_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4962 (class 2606 OID 17748)
-- Name: jenis FK_jenis_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis
    ADD CONSTRAINT "FK_jenis_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4963 (class 2606 OID 17758)
-- Name: jenis FK_jenis_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis
    ADD CONSTRAINT "FK_jenis_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4964 (class 2606 OID 17753)
-- Name: jenis FK_jenis_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis
    ADD CONSTRAINT "FK_jenis_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4965 (class 2606 OID 17776)
-- Name: lokasi FK_lokasi_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi
    ADD CONSTRAINT "FK_lokasi_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4966 (class 2606 OID 17786)
-- Name: lokasi FK_lokasi_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi
    ADD CONSTRAINT "FK_lokasi_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4967 (class 2606 OID 17781)
-- Name: lokasi FK_lokasi_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lokasi
    ADD CONSTRAINT "FK_lokasi_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4968 (class 2606 OID 17803)
-- Name: merk FK_merk_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk
    ADD CONSTRAINT "FK_merk_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4969 (class 2606 OID 17813)
-- Name: merk FK_merk_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk
    ADD CONSTRAINT "FK_merk_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4970 (class 2606 OID 17808)
-- Name: merk FK_merk_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.merk
    ADD CONSTRAINT "FK_merk_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4958 (class 2606 OID 17720)
-- Name: user FK_user_user_createdby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT "FK_user_user_createdby" FOREIGN KEY (createdby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4959 (class 2606 OID 17730)
-- Name: user FK_user_user_deletedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT "FK_user_user_deletedby" FOREIGN KEY (deletedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4960 (class 2606 OID 17725)
-- Name: user FK_user_user_updatedby; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT "FK_user_user_updatedby" FOREIGN KEY (updatedby) REFERENCES public."user"(userid) ON DELETE SET NULL;


--
-- TOC entry 4983 (class 2606 OID 18039)
-- Name: asetmove asetmove_lokasiakhirid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT asetmove_lokasiakhirid_foreign FOREIGN KEY (lokasiakhirid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4984 (class 2606 OID 18034)
-- Name: asetmove asetmove_lokasiawalid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetmove
    ADD CONSTRAINT asetmove_lokasiawalid_foreign FOREIGN KEY (lokasiawalid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4989 (class 2606 OID 18004)
-- Name: asetservice asetservice_asetid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_asetid_foreign FOREIGN KEY (asetid) REFERENCES public.aset(asetid);


--
-- TOC entry 4990 (class 2606 OID 18019)
-- Name: asetservice asetservice_createdby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_createdby_foreign FOREIGN KEY (createdby) REFERENCES public."user"(userid);


--
-- TOC entry 4991 (class 2606 OID 18029)
-- Name: asetservice asetservice_deletedby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_deletedby_foreign FOREIGN KEY (deletedby) REFERENCES public."user"(userid);


--
-- TOC entry 4992 (class 2606 OID 18059)
-- Name: asetservice asetservice_lokasisebelum_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_lokasisebelum_fk FOREIGN KEY (lokasisebelumserviceid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4993 (class 2606 OID 18014)
-- Name: asetservice asetservice_servicestatusid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_servicestatusid_foreign FOREIGN KEY (servicestatusid) REFERENCES public.servicestatus(servicestatusid);


--
-- TOC entry 4994 (class 2606 OID 18024)
-- Name: asetservice asetservice_updateby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_updateby_foreign FOREIGN KEY (updateby) REFERENCES public."user"(userid);


--
-- TOC entry 4995 (class 2606 OID 18009)
-- Name: asetservice asetservice_vendorid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservice
    ADD CONSTRAINT asetservice_vendorid_foreign FOREIGN KEY (vendorid) REFERENCES public.vendor(vendorid);


--
-- TOC entry 4996 (class 2606 OID 18082)
-- Name: asetservicedt asetservicedt_asetserviceid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservicedt
    ADD CONSTRAINT asetservicedt_asetserviceid_foreign FOREIGN KEY (asetserviceid) REFERENCES public.asetservice(asetserviceid);


--
-- TOC entry 4997 (class 2606 OID 18087)
-- Name: asetservicedt asetservicedt_createdby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservicedt
    ADD CONSTRAINT asetservicedt_createdby_foreign FOREIGN KEY (createdby) REFERENCES public."user"(userid);


--
-- TOC entry 4998 (class 2606 OID 18097)
-- Name: asetservicedt asetservicedt_deletedby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservicedt
    ADD CONSTRAINT asetservicedt_deletedby_foreign FOREIGN KEY (deletedby) REFERENCES public."user"(userid);


--
-- TOC entry 4999 (class 2606 OID 18092)
-- Name: asetservicedt asetservicedt_updatedby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asetservicedt
    ADD CONSTRAINT asetservicedt_updatedby_foreign FOREIGN KEY (updatedby) REFERENCES public."user"(userid);


--
-- TOC entry 4961 (class 2606 OID 17984)
-- Name: user user_userlevelid_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_userlevelid_foreign FOREIGN KEY (userlevelid) REFERENCES public.userlevel(userlevelid);


--
-- TOC entry 4985 (class 2606 OID 17989)
-- Name: vendor vendor_createdby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_createdby_foreign FOREIGN KEY (createdby) REFERENCES public."user"(userid);


--
-- TOC entry 4986 (class 2606 OID 17999)
-- Name: vendor vendor_deletedby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_deletedby_foreign FOREIGN KEY (deletedby) REFERENCES public."user"(userid);


--
-- TOC entry 4987 (class 2606 OID 18054)
-- Name: vendor vendor_lokasiid_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_lokasiid_fk FOREIGN KEY (lokasiid) REFERENCES public.lokasi(lokasiid);


--
-- TOC entry 4988 (class 2606 OID 17994)
-- Name: vendor vendor_updateby_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendor
    ADD CONSTRAINT vendor_updateby_foreign FOREIGN KEY (updatedby) REFERENCES public."user"(userid);


-- Completed on 2026-02-26 06:35:05

--
-- PostgreSQL database dump complete
--

\unrestrict 1j0HSqlbeRUNNhDRExhgi0y3Ijefbanp0EhoMQ5r7XWyMxSMVFOu5g9GCTkJhlw

