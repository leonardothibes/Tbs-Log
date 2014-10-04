# Makefile - A courtesy of Tbs\Log.
#
# A collection of reusable tasks for automating some process.
#
# Make sure you have GNU Make, and type `make` in this Makefile folder.
#

# General Configuration
NAME          = "Tbs\\Log"
CODE-STANDARD = "PSR2"
DATE          = `date "+%Y-%m-%d"`
BASEDIR       = `pwd`
BIN           = "${BASEDIR}/bin"
SRC           = "${BASEDIR}/src"
DOCS          = "${BASEDIR}/docs"
BUILD         = "${BASEDIR}/build"
TESTS         = "${BASEDIR}/tests"
VENDOR        = "${BASEDIR}/vendor"
LOGS          = "${BASEDIR}/logs"
LOGFILE       = "${LOGS}/debug_${DATE}.log"
PHPUNIT       = "${BIN}/phpunit"
PHPCS         = "${BIN}/phpcs"
URI           = "leonardothibes/Tbs-Log"
DOCUMENTUP    = "http://documentup.com/${URI}"
GITHUB        = "http://github.com/${URI}"

build: .clear .title lint test-analyze

rw:
	@[ -d ${BUILD}   ] || mkdir ${BUILD}
	@[ -d ${LOGS}    ] || mkdir ${LOGS}
	@[ -f ${LOGFILE} ] || > ${LOGFILE}
	@chmod -R 777 ${LOGS}

clean:
	@rm -Rf ${LOGS}/*
	@rm -Rf ${BUILD}/*
	@find ${BASEDIR} | grep .DS_Store | xargs rm -f
	@find ${BASEDIR} | grep Thumbs.db | xargs rm -f

clean-all:
	@rm -Rf ${VENDOR}
	@rm -Rf ${BUILD}
	@rm -Rf ${LOGS}
	@rm -Rf ${BIN}

.composer:
	@[ -d ${BIN} ] || mkdir ${BIN}
	@if [ ! -f ${BIN}/composer.phar ]; then \
		curl -sS https://getcomposer.org/installer | php -- --install-dir=${BIN}; \
	fi; \

install: .clear .composer
	@php ${BIN}/composer.phar install --no-dev

install-dev: .clear .composer
	@php ${BIN}/composer.phar install --dev

classmap:
	@php ${BIN}/composer.phar dump-autoload

lint: .clear
	@for file in `find ./src` ; do \
		results=`php -l $$file`; \
		if [ "$$results" != "No syntax errors detected in $$file" ]; then \
			echo $$results; \
			echo ""; \
			exit 1; \
		fi; \
	done;
	@echo "No syntax errors detected"
	
test: .clear rw
	@${PHPUNIT} -c ${TESTS}/phpunit.xml ${TESTS}

testdox: rw
	@${PHPUNIT} -c ${TESTS}/phpunit.xml --testdox ${TESTS}

test-analyze: .clear rw
	@${PHPUNIT} -c ${TESTS}/phpunit.xml --testdox-html=${BUILD}/testdox.html --coverage-html=${BUILD}/coverage ${TESTS}

code-sniffer:

pdepend:

phpmd:

phpcpd:

phpdcd:

phpdoc:

documentup:

docs:

.clear:
	@clear

.title:
	@echo "Makefile - A courtesy of ${NAME}."
	@echo ""

help: .clear
	@echo "Usage: make [options]"
	@echo ""
	@echo "  main(default)     General project build"
	@echo "  help              Show this HELP message"
	@echo ""