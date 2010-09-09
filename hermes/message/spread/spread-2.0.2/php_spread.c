/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2006 George Schlossnagle, The PHP Group           |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt.                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: George Schlossnagle <george@omniti.com>                      |
  |         Pierre-A. Joye <pierre@php.net>                              |
  |         Rob Richards <rrichards@php.net>                             |
  +----------------------------------------------------------------------+
*/
/* $Id: php_spread.c,v 1.30 2008/06/09 13:24:23 rrichards Exp $ */

#include "config.h"

#include "php_spread.h"
#include "php_globals.h"
#include "php.h"
#include "ext/standard/php_string.h"
#include "ext/standard/info.h"

#include <sp.h>
#include <sp_func.h>
#include <sys/time.h>

#define ACK fprintf(stderr, "%s:%d\n", __FILE__, __LINE__)

/* for function declarations */

/* This comes from php install tree */
#include "ext/standard/info.h"

static int le_conn;

/* {{{ spread_functions[] */

function_entry spread_functions[] = {
	PHP_FE(spread_connect, NULL)
	PHP_FE(spread_multicast, NULL)
	PHP_FE(spread_disconnect, NULL)
	PHP_FE(spread_join, NULL)
	PHP_FE(spread_receive, NULL)
	{NULL, NULL, NULL}
};
/* }}} */

#ifdef ZEND_ENGINE_2
zend_class_entry *spread_class_entry;
static zend_object_handlers spread_object_handlers;

/* {{{ spread_fetch_object()
 */
static inline ze_spread_object *spread_fetch_object(zval *object TSRMLS_DC)
{
	return (ze_spread_object *) zend_object_store_get_object(object TSRMLS_CC);
}
/* }}} */

/* {{{ spread_object_free_storage */
static void spread_object_free_storage(void *object TSRMLS_DC)
{
	ze_spread_object * intern = (ze_spread_object *) object;

	int i;

	if (!intern) {
		return;
	}

	if (intern->mbox) {
		SP_disconnect(*intern->mbox);
		efree(intern->mbox);
		intern->mbox = NULL;
	}

	if (intern->zo.properties) {
		zend_hash_destroy(intern->zo.properties);
		FREE_HASHTABLE(intern->zo.properties);
	}

	efree(intern);
}
/* }}} */

/* {{{ spread_object_new */
PHP_SPREAD_API  zend_object_value spread_object_new(zend_class_entry *class_type TSRMLS_DC)
{
	ze_spread_object *intern;
	zval *tmp;
	zend_object_value retval;

	intern = emalloc(sizeof(ze_spread_object));
	memset(&intern->zo, 0, sizeof(zend_object));

	intern->zo.ce = class_type;

	intern->mbox = 0;

	ALLOC_HASHTABLE(intern->zo.properties);
	zend_hash_init(intern->zo.properties, 0, NULL, ZVAL_PTR_DTOR, 0);
	zend_hash_copy(intern->zo.properties, &class_type->default_properties, (copy_ctor_func_t) zval_add_ref, (void *) &tmp, sizeof(zval *));

	retval.handle = zend_objects_store_put(intern, NULL, (zend_objects_free_object_storage_t) spread_object_free_storage, NULL TSRMLS_CC);

	retval.handlers = (zend_object_handlers *) & spread_object_handlers;

	return retval;
}
/* }}} */

/* {{{ spread_class_functions */
static zend_function_entry spread_class_functions[] = {
	PHP_ME_MAPPING(connect,         spread_connect,         NULL,  0)
	PHP_ME_MAPPING(disconnect,      spread_disconnect,      NULL,  0)
	PHP_ME_MAPPING(join,            spread_join,            NULL,  0)
	PHP_ME_MAPPING(receive,         spread_receive,         NULL,  0)
	PHP_ME_MAPPING(multicast,       spread_multicast,       NULL,  0)
	{NULL, NULL, NULL, 0}
};
/* }}} */
#endif

#ifdef ZEND_ENGINE_2
/* {{{ REGISTER_ZIP_CLASS_CONST_LONG */
#define REGISTER_SPREAD_CLASS_CONST_LONG(const_name, value) \
	zend_declare_class_constant_long(spread_class_entry, const_name, sizeof(const_name)-1, (long)value TSRMLS_CC);
/* }}} */
#endif

/* list of exported functions */
/* {{{ module entry */
zend_module_entry spread_module_entry = {
	STANDARD_MODULE_HEADER,
	"spread",
	spread_functions,
	PHP_MINIT(spread),
	PHP_MSHUTDOWN(spread),
	NULL,
	NULL,
	PHP_MINFO(spread),
	PHP_SPREAD_VERSION,
	STANDARD_MODULE_PROPERTIES
};

/* }}} */

#ifdef COMPILE_DL_SPREAD
ZEND_GET_MODULE(spread)
#endif

/* UTILITIES */

static void php_spread_rsr_dtor(zend_rsrc_list_entry *rsrc TSRMLS_DC) /* {{{ */
{
	mailbox *mbox = (mailbox *)rsrc->ptr;

	if (mbox) {
		SP_disconnect(*mbox);
		efree(mbox);
		rsrc->ptr = NULL;
	}
}
/* }}} */

/* initialization file support */

/* {{{ MINIT */
PHP_MINIT_FUNCTION(spread)
{
#if ZEND_EXTENSION_API_NO >= 220051025 
	int i;

	zend_class_entry spread_ce;

	memcpy(&spread_object_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));

	spread_object_handlers.clone_obj      = NULL;

	INIT_CLASS_ENTRY(spread_ce, "Spread", spread_class_functions);
	spread_ce.create_object = spread_object_new;

	spread_class_entry = zend_register_internal_class(&spread_ce TSRMLS_CC);

	REGISTER_SPREAD_CLASS_CONST_LONG("ILLEGAL_SPREAD",		ILLEGAL_SPREAD);
	REGISTER_SPREAD_CLASS_CONST_LONG("COULD_NOT_CONNECT",	COULD_NOT_CONNECT);
	REGISTER_SPREAD_CLASS_CONST_LONG("CONNECTION_CLOSED",	CONNECTION_CLOSED);
	REGISTER_SPREAD_CLASS_CONST_LONG("REJECT_VERSION",		REJECT_VERSION);
	REGISTER_SPREAD_CLASS_CONST_LONG("REJECT_NO_NAME",		REJECT_NO_NAME);
	REGISTER_SPREAD_CLASS_CONST_LONG("REJECT_ILLEGAL_NAME", REJECT_ILLEGAL_NAME);
	REGISTER_SPREAD_CLASS_CONST_LONG("REJECT_NOT_UNIQUE",	REJECT_NOT_UNIQUE);

	REGISTER_SPREAD_CLASS_CONST_LONG("LOW_PRIORITY",		LOW_PRIORITY);
	REGISTER_SPREAD_CLASS_CONST_LONG("MEDIUM_PRIORITY", 	MEDIUM_PRIORITY);
	REGISTER_SPREAD_CLASS_CONST_LONG("HIGH_PRIORITY",		HIGH_PRIORITY);

	REGISTER_SPREAD_CLASS_CONST_LONG("UNRELIABLE_MESS",		UNRELIABLE_MESS);
	REGISTER_SPREAD_CLASS_CONST_LONG("RELIABLE_MESS",		RELIABLE_MESS);
	REGISTER_SPREAD_CLASS_CONST_LONG("FIFO_MESS",			FIFO_MESS);
	REGISTER_SPREAD_CLASS_CONST_LONG("CAUSAL_MESS",			CAUSAL_MESS);
	REGISTER_SPREAD_CLASS_CONST_LONG("AGREED_MESS",			AGREED_MESS);
	REGISTER_SPREAD_CLASS_CONST_LONG("SAFE_MESS",			SAFE_MESS);
	REGISTER_SPREAD_CLASS_CONST_LONG("REGULAR_MESS",		REGULAR_MESS);

	REGISTER_SPREAD_CLASS_CONST_LONG("REG_MEMB_MESS",			REG_MEMB_MESS);
	REGISTER_SPREAD_CLASS_CONST_LONG("TRANSITION_MESS",			TRANSITION_MESS);
	REGISTER_SPREAD_CLASS_CONST_LONG("CAUSED_BY_JOIN",			CAUSED_BY_JOIN);
	REGISTER_SPREAD_CLASS_CONST_LONG("CAUSED_BY_LEAVE",			CAUSED_BY_LEAVE);
	REGISTER_SPREAD_CLASS_CONST_LONG("CAUSED_BY_DISCONNECT",	CAUSED_BY_DISCONNECT);
	REGISTER_SPREAD_CLASS_CONST_LONG("CAUSED_BY_NETWORK",		CAUSED_BY_NETWORK);
	REGISTER_SPREAD_CLASS_CONST_LONG("MEMBERSHIP_MESS",			MEMBERSHIP_MESS);
#else
	REGISTER_LONG_CONSTANT("ILLEGAL_SPREAD",		ILLEGAL_SPREAD,			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("COULD_NOT_CONNECT",		COULD_NOT_CONNECT,		CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("CONNECTION_CLOSED",		CONNECTION_CLOSED,		CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("REJECT_VERSION",		REJECT_VERSION,			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("REJECT_NO_NAME",		REJECT_NO_NAME,			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("REJECT_ILLEGAL_NAME",	REJECT_ILLEGAL_NAME, 	CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("REJECT_NOT_UNIQUE",		REJECT_NOT_UNIQUE,		CONST_CS | CONST_PERSISTENT);

	REGISTER_LONG_CONSTANT("SP_LOW_PRIORITY",		LOW_PRIORITY,			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_MEDIUM_PRIORITY",	MEDIUM_PRIORITY,		CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_HIGH_PRIORITY",		HIGH_PRIORITY,			CONST_CS | CONST_PERSISTENT);

	REGISTER_LONG_CONSTANT("SP_UNRELIABLE_MESS",	UNRELIABLE_MESS,		CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_RELIABLE_MESS",		RELIABLE_MESS,			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_FIFO_MESS",			FIFO_MESS,				CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_CAUSAL_MESS",		CAUSAL_MESS,			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_AGREED_MESS",		AGREED_MESS,			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_SAFE_MESS",			SAFE_MESS,				CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_REGULAR_MESS",		REGULAR_MESS,			CONST_CS | CONST_PERSISTENT);

	REGISTER_LONG_CONSTANT("SP_REG_MEMB_MESS",		REG_MEMB_MESS,			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_TRANSITION_MESS",	TRANSITION_MESS,		CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_CAUSED_BY_JOIN",		CAUSED_BY_JOIN,			CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_CAUSED_BY_LEAVE",	CAUSED_BY_LEAVE,		CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_CAUSED_BY_DISCONNECT", CAUSED_BY_DISCONNECT, CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_CAUSED_BY_NETWORK",	CAUSED_BY_NETWORK,		CONST_CS | CONST_PERSISTENT);
	REGISTER_LONG_CONSTANT("SP_MEMBERSHIP_MESS",	MEMBERSHIP_MESS,		CONST_CS | CONST_PERSISTENT);
#endif
	le_conn = zend_register_list_destructors_ex(php_spread_rsr_dtor, NULL, "spread", module_number);
	return SUCCESS;
}
/* }}} */

/* {{{ MSHUTDOWN */
PHP_MSHUTDOWN_FUNCTION(spread)
{
	return SUCCESS;
}
/* }}} */

/* {{{ MINFO */
PHP_MINFO_FUNCTION(spread)
{
	char sp_version[128];
	int     mver, miver, pver;

	php_info_print_table_start();
	{
		php_info_print_table_row(2, "Spread", "enabled");
		php_info_print_table_row(2, "Version", PHP_SPREAD_VERSION);
		php_info_print_table_row(2, "CVS Id", "$Id: php_spread.c,v 1.30 2008/06/09 13:24:23 rrichards Exp $");
		if (SP_version( &mver, &miver, &pver)) {
			snprintf(sp_version, 128, "%d.%d.%d", mver, miver, pver);
			php_info_print_table_row(2, "Spread version", sp_version);
		}
	}
	php_info_print_table_end();
}
/* }}} */


/* {{{  int _connect(char *spread_name, char *private_name TSRMLS_DC)
 * Open a persistent connection to a spread daemon.  Returns resource id# */
static mailbox *_phpspread_connect(char *spread_name, char *private_name TSRMLS_DC)
{
	mailbox *mbox;
	char private_group[MAX_GROUP_NAME];
	char *hashed_details;
	int hashed_details_length;
	int retval;

	hashed_details_length = sizeof("spread__") + strlen(spread_name) + strlen(private_name);
	hashed_details = (char *) emalloc(hashed_details_length);

	sprintf(hashed_details, "spread_%s_%s", spread_name, private_name);

	mbox = emalloc(sizeof(mailbox));

	retval = SP_connect(spread_name, private_name, 0, 0, mbox, private_group);

	if (retval != ACCEPT_SESSION) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Failed to connect to spread daemon (%s) using private_name (%s), error returned was: %d", spread_name, private_name, retval);
		efree(hashed_details);
		efree(mbox);
		return 0;
	}

	efree(hashed_details);

	return mbox;
}
/* }}} */

/* {{{ proto int spread_connect(string spreaddaemon [, string private_name])
  Open a persistent connection to a spread daemon.  Returns resource id# */
PHP_FUNCTION(spread_connect)
{
	char *spread_name = NULL;
	char *private_name = NULL;
	int spread_name_len;
	int private_name_len;
	mailbox *mbox = NULL;
	char private_group[MAX_GROUP_NAME];
	char *hashed_details;
	int hashed_details_length;
	int rsrc_id;
	list_entry *le;

#ifdef ZEND_ENGINE_2
	zval *this = getThis();
	ze_spread_object *obj;
	if (this) {
		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s|s", &spread_name, &spread_name_len, &private_name, &private_name_len) == FAILURE) {
			return;
		}
		obj = spread_fetch_object(this TSRMLS_CC);
	} else
#endif
	{
		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s|s", &spread_name, &spread_name_len, &private_name, &private_name_len) == FAILURE) {
			return;
		}
	}

	if(!private_name) {
		char default_private_name[MAX_PRIVATE_NAME];

		snprintf(default_private_name, MAX_PRIVATE_NAME, "php-%05d", getpid());
		private_name = default_private_name;
	} else {
		if (private_name_len > MAX_PRIVATE_NAME) {
			RETURN_LONG(REJECT_ILLEGAL_NAME);
		}
	}

	mbox = _phpspread_connect(spread_name, private_name TSRMLS_CC);
	if (mbox) {
#ifdef ZEND_ENGINE_2
		if (this) {

			zval *groups_array;

			obj->mbox = mbox;	

			add_property_string(this, "daemon", spread_name, 1);
			add_property_string(this, "private_name", private_name, 1);
			add_property_bool(this, "connected", 1);

			MAKE_STD_ZVAL(groups_array);
			array_init(groups_array);
			add_property_zval(this_ptr, "joined_groups", groups_array);

			/* add_prop_zval increments the refcount, useless here */
			zval_ptr_dtor(&groups_array);
			RETURN_TRUE;
		} else
#endif
		{
			ZEND_REGISTER_RESOURCE(return_value, mbox, le_conn);
		}
	} else {
		RETURN_FALSE;
	}
}
/* }}} */

/* {{{ proto int spread_multicast(resource id, zval group, string message [, int service_type [, int message_type]])
  Send message to one or more groups */
PHP_FUNCTION(spread_multicast) {
	zval *group;
	zval *optional_mbox;
	int *mbox;
	char *message;
	int sperrno, message_len;
	long service_type = RELIABLE_MESS;
	long mess_type = 1;

#ifdef ZEND_ENGINE_2
	zval *this = getThis();
	if (this) {
		ze_spread_object *obj = spread_fetch_object(this TSRMLS_CC);
		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "zs|ll",  &group, &message, &message_len, &service_type, &mess_type) == FAILURE) {
			return;
		}

		mbox = obj->mbox;
	} else
#endif
	{
		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rzs|ll",  &optional_mbox, &group, &message, &message_len, &service_type, &mess_type) == FAILURE) {
			return;
		}

		ZEND_FETCH_RESOURCE(mbox, mailbox *, &optional_mbox, -1, "Spread-FD", le_conn);
	}

	if (! mbox) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Invalid Spread Connection.");
		RETURN_FALSE;
	}
	
	if (mess_type < SHRT_MIN || mess_type > SHRT_MAX) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Message type value out of range.");
		RETURN_FALSE;
	}

	if(Z_TYPE_P(group) == IS_STRING) {
		sperrno = SP_multicast(*mbox, (int)service_type, Z_STRVAL_P(group), (int16) mess_type, message_len, message);
		if (sperrno < 0) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "SP_mulicast error(%d)", sperrno);
			RETURN_FALSE;
		}
		RETURN_LONG(sperrno);
	} else if(Z_TYPE_P(group) == IS_ARRAY) {
		char groupnames[100][MAX_GROUP_NAME];
		HashPosition pos;
		zval *tmparr, **tmp;
		int n = 0;

		zend_hash_internal_pointer_reset_ex(group->value.ht, &pos);
		while(zend_hash_get_current_data_ex(group->value.ht, (void **) &tmp, &pos) == SUCCESS && n < 100) {
			convert_to_string_ex(tmp);
			strncpy(groupnames[n], Z_STRVAL_PP(tmp), MAX_GROUP_NAME);
			n++;
			zend_hash_move_forward_ex(group->value.ht, &pos);
		}
		sperrno = SP_multigroup_multicast(*mbox, (int)service_type, n, (const char (*)[MAX_GROUP_NAME]) groupnames, (int16) mess_type, message_len, message);
		if(sperrno < 0) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "SP_mulicast error(%d)", sperrno);
			RETURN_FALSE;
		}

		RETURN_LONG(sperrno);
	} else {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "SP_Join: expect groups to an array of strings or a string (got: %d)", Z_TYPE_P(group));
		RETURN_FALSE;
	}
}
/* }}} */

/* {{{ proto int spread_join(resource id, zval group)
  Join one or more groups */
PHP_FUNCTION(spread_join) {
	zval *group, *mbox_zval;
	zval **joined_groups = NULL;
	mailbox *mbox;
	int sperrno;

#ifdef ZEND_ENGINE_2
	zval *this = getThis();
	
	if (this) {
		ze_spread_object *obj = spread_fetch_object(this TSRMLS_CC);

		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &group) == FAILURE) {
			return;
		}

		if(zend_hash_find(obj->zo.properties, "joined_groups", sizeof("joined_groups"), (void **)&joined_groups) == FAILURE) {
			RETURN_FALSE;
		}
		mbox = obj->mbox;
	} else
#endif
	{
		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rz", &mbox_zval, &group) == FAILURE) {
			return;
		}
		ZEND_FETCH_RESOURCE(mbox, mailbox *, &mbox_zval, -1, "spread", le_conn);
	}

	if (!mbox) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Resource or mailbox connection invalid.");
		RETURN_FALSE;
	}

	if(Z_TYPE_P(group) == IS_STRING) {
		if( (sperrno = SP_join(*mbox, Z_STRVAL_P(group))) < 0) {
			RETURN_LONG(sperrno);
		}

#ifdef ZEND_ENGINE_2
		if (joined_groups != NULL) {
			add_next_index_string(*joined_groups, Z_STRVAL_P(group), 1);
		}
#endif
		RETURN_TRUE;
	} else if(Z_TYPE_P(group) == IS_ARRAY) {
		char groupnames[100][MAX_GROUP_NAME];
		HashPosition pos;
		HashTable *tmparr_hash;
		zval **tmp;
		int error = 0, n = 0;

		tmparr_hash = Z_ARRVAL_P(group);

		if (zend_hash_num_elements(tmparr_hash) < 1) {
			RETURN_TRUE;
		}

		for (zend_hash_internal_pointer_reset_ex(tmparr_hash, &pos);
				zend_hash_get_current_data_ex(tmparr_hash,(void**) &tmp, &pos) == SUCCESS && (n++ <= 100);
				zend_hash_move_forward_ex(tmparr_hash, &pos)) {

			convert_to_string_ex(tmp);
			if (Z_STRLEN_PP(tmp)< 1 || Z_STRLEN_PP(tmp) > MAX_GROUP_NAME) {
				RETURN_LONG(ILLEGAL_GROUP);
			}

			if((sperrno = SP_join(*mbox,  Z_STRVAL_PP(tmp))) < 0) {
				error = sperrno;
				break;
			}

#ifdef ZEND_ENGINE_2
			if (joined_groups != NULL) {
				add_next_index_string(*joined_groups, Z_STRVAL_PP(tmp), 1);
			}
#endif
		}

		if (error) {
			RETURN_LONG(sperrno);
		} else {
			RETURN_TRUE;
		}
	} else {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "SP_Join: expect groups to an array of strings or a string (got: %d)", Z_TYPE_P(group));
		RETURN_LONG(ILLEGAL_GROUP);
	}
}
/* }}} */

/* {{{ proto int spread_receive(resource id [,int timeout])
  Join one or more groups */
PHP_FUNCTION(spread_receive) {
	zval *mbox_zval, *groups_zval;
	int id = -1;
	int *mbox;
	double timeout = 0;
	struct timeval towait;
	int sperrno;
	int i, endmis, ret, ngrps, msize=0;
	int16 mtype;
	service stype;
	int oldmsize = 0;
	int oldgsize = 0;
	int newmsize = (1<<15);
	int newgsize = (1<<6);
	char* groups=NULL;
	char* mess=NULL;
	char sender[MAX_GROUP_NAME];
	fd_set readfs;

#ifdef ZEND_ENGINE_2
	zval *this = getThis();

	if (this) {
		ze_spread_object *obj = spread_fetch_object(this TSRMLS_CC);

		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "|d", &timeout) == FAILURE) {
			return;
		}
		mbox = obj->mbox;
	} else
#endif
	{ 
		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r|d", &mbox_zval, &timeout) == FAILURE) {
			return;
		}
		ZEND_FETCH_RESOURCE(mbox, mailbox *, &mbox_zval, id, "Spread-FD", le_conn);
	}

	if (!mbox) {
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "%s", "Resource or mailbox connection invalid.");
		RETURN_FALSE;
	}

	FD_ZERO(&readfs);
	FD_SET(*mbox, &readfs);

	if (-1 == timeout) {
		ret = select(*mbox+1, &readfs, NULL, &readfs, NULL);
	} else {
		towait.tv_sec = (unsigned long)timeout;
		towait.tv_usec = (unsigned long)(1000000.0 * (timeout - (double)towait.tv_sec));
		ret = select(*mbox+1, &readfs, NULL, &readfs, &towait);
	}

 	if(1 != ret) {
		RETURN_FALSE;
	}

try_again:
	{
		if(oldgsize != newgsize) {
			if(groups) {
				groups = (char*) erealloc(groups, newgsize * MAX_GROUP_NAME);
			} else {
				groups = (char*) emalloc(newgsize * MAX_GROUP_NAME);
			}
			oldgsize=newgsize;
		}
		if(oldmsize != newmsize) {
			if(mess) {
				mess = (char *) erealloc(mess, newmsize);
			} else {
				mess = (char *) emalloc(newmsize);
			}
			oldmsize = newmsize;
		}

		ret = SP_receive(*mbox, &stype, sender, newgsize, &ngrps, 
			(char (*)[MAX_GROUP_NAME])groups, &mtype, &endmis, newmsize, mess);

		if(ret < 0) {
			if(ret==BUFFER_TOO_SHORT) {
				newmsize=-endmis;
				newmsize++;
				goto try_again;
			}
		} else {
			if (newmsize != (ret + 1)) {
				mess = (char *) erealloc(mess, ret + 1);
			}
		}
		msize = ret;
	}

	if ((ret < 0) || (array_init(return_value)==FAILURE)) {
		if (groups) {
			efree(groups);
		}
		if (mess) {
			efree(mess);
		}
		RETURN_FALSE;
	}

	/* spread does not null termintae these, so we must */
	mess[msize] = '\0';
	add_assoc_stringl(return_value, "message", mess, msize, 0);

	MAKE_STD_ZVAL(groups_zval);
	array_init(groups_zval);
	for(i = 0; i < ngrps; i++) {
		add_index_stringl(groups_zval, i, &groups[i*MAX_GROUP_NAME], strlen(&groups[i*MAX_GROUP_NAME]), 1);
	}

	if (groups) {
		efree(groups);
	}
	add_assoc_zval(return_value, "groups", groups_zval); 
	add_assoc_long(return_value, "message_type", mtype); 
	add_assoc_string(return_value, "sender", sender, 1); 
}
/* }}} */

/* {{{ proto int spread_close(resource id)
   Close a spread connection */
PHP_FUNCTION(spread_disconnect) {
	zval *spread_conn;
	mailbox *mbox;
	int retval;

#ifdef ZEND_ENGINE_2
	zval *this = getThis();
	if (this) {
		ze_spread_object *obj = spread_fetch_object(this TSRMLS_CC);

		mbox = obj->mbox;
		if (mbox) {
			int retval;

			retval = SP_disconnect(*mbox);
			zend_update_property_bool(spread_class_entry, this, "connected", sizeof("connected") - 1, 0 TSRMLS_CC);

			if (retval == 0) {
				RETURN_TRUE;
			} else {
				RETURN_LONG(retval);
			}
		}
	} else 
#endif
	{
		if(zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &spread_conn) == FAILURE) {
			return;
		}
		ZEND_FETCH_RESOURCE(mbox, mailbox *, &spread_conn, -1, "Spread-FD", le_conn);

		if (!mbox) {
			php_error_docref(NULL TSRMLS_CC, E_WARNING, "%s", "Invalid spread resource");
			RETURN_FALSE;
		}

		retval = SP_disconnect(*mbox);

		zend_list_delete(Z_RESVAL_P(spread_conn));
		if (retval == 0) {
			RETURN_TRUE;
		} else {
			RETURN_LONG(retval);
		}
	}
}
/* }}} */
